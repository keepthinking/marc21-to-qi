<?php

if(!function_exists('print_image_size'))
{
	function print_image_size(stdClass $image, $width, $height)
	{
		// Error scenario: when the image has no dimensions. Probably means the image was imported through some other mechanism which doesn't know the image size. In this case print nothing
		if (!$image->width || !$image->height)
		{
			return "";
		}
	
		/*
		 * We have 4 scenarios:
		 * 1. We have a target width and height.
		 * 2. We have a target width, no height
		 * 3. We have a target height, no width.
		 * 4. We have neight width nor height. In this case most likely something went wrong, but we'll just return the original sizes
		 * 
		 * In case 1, divide width by height of source and target sizes, store as ratio.
		 * 	If source ratio is higher, factor = source_height/target_height
		 * 	If target ratio is higher, factor = source_width/target_width
		 *	If ratios are the same it doesn't matter which of the two above you pick.
		 * In case 2, factor = source_width/target_width 
		 * In case 3, factor = source_height/target_height 
		 * For all cases, given factor, divide both source width and source height by factor
		 */
		if ($width && $height)
		{
			$ratio_source = $image->width/$image->height;
			$ratio_target = $width/$height;
			if ($ratio_source > $ratio_target)
				$factor = $image->height/$height;
			else
				$factor = $image->width/$width;
		}
		else if ($width)
		{
			$factor = $image->width/$width;
		}
		else if ($height)
		{
			$factor = $image->height/$height;
		}
		else
		{
			$factor = 1;
		}
	
		// Now that we have the factor, calculate the image sizes to print
		$w = round($image->width/$factor);
		$h = round($image->height/$factor);
	
		return "width=\"{$w}\" height=\"{$h}\"";
	}
}

// --------------------------------------------------------------------

/**
 * Split Array
 * Split an array into pieces
 *
 * @param $list Array The array to split up
 * @param $per_column int
 * @return array[]
 * 
 **/
if(!function_exists('media_split_array'))
{
	function media_split_array(Array $list, $columns = 5)
	{
		$return = array();
		do
		{
			$return[] = array_splice($list, 0, $columns);
		} while (count($list));
		return $return;
	}
}

// --------------------------------------------------------------------

/**
 * Given an array of media folders, group them by whether they have a rank or not
 * Also alter the name of those with rank to indicate it more clearly. If support
 * media catalogue exists folders will be grouped by catalogue. 
 *
 * Should be used when media folders are used in a dropdown
 *
 * @param array The initial array
 * @return array
**/
function media_folder_group(Array $folders)
{
	$supports_media_catalogue = get_instance()->db->table_exists("media_catalogue");
	$return = array();
	
	if($supports_media_catalogue)
	{
		foreach($folders as $item)
		{
			$item->name = ($item->rank ? "{$item->rank}. ":"") . $item->name;
			$return[$item->catalogue_name][] = $item;
		}
		ksort($return);
	}
	else
	{
		$ranked = array();
		$unranked = array();
		foreach ($folders as $item)
		{
			if ($item->rank)
			{
				$item->name .= " ({$item->rank})";
				$ranked[] = $item;
			}
			else
				$unranked[] = $item;
		}
		$return = array(
			lang('ranked') => $ranked,
			lang('unranked') => $unranked
		);
	}
	
	return array_filter($return);
}

// --------------------------------------------------------------------

/**
 * Is Allowed File
 *
 * @param string $media_type
 * @param string $filename
 * @param string $relationship_type
 * @return bool
**/
function is_allowed_file($media_type, $filename, $relationship_type = null) 
{
	$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
	$allowed_types = allowed_file_types($media_type, $relationship_type);
	
	return in_array($extension, explode('|', $allowed_types));
}

// --------------------------------------------------------------------

/**
 * Allowed File Types
 *
 * @param string $media_type
 * @param string $relationship_type
 * @return bool
**/
function allowed_file_types($media_type, $relationship_type = null) 
{
	$config = config("upload");
	$allowed_types = $config[$media_type]['allowed_types'];
	
	switch($relationship_type)
	{
		case 'cover_image':
			$allowed_types = $config['image']['allowed_types'];
			break;
		case 'subtitle':
			$allowed_types = "sub|srt";
			break;
		case 'document':
			$allowed_types = $config['file']['allowed_types'];
			break;
	}
	
	return strtolower($allowed_types);
}

// --------------------------------------------------------------------

/**
 * Has Media Library Rights
 *
 * @param int $media_catalogue_id
 * @param int $can : 0 = no_access, 1 = read_only, 2 = full_access
 * @return bool
**/
function has_medialib_rights($media_catalogue_id = null, $can = null)
{
	$rights = @get_instance()->data->permissions->media;
	
	if(empty($rights))
		return false;
		
	if(!empty($media_catalogue_id) && !empty($can))
	{
		if(!empty($rights[$media_catalogue_id]))
		{
			$permission = $rights[$media_catalogue_id];
			list($right, $width, $height) = explode(':', $permission);
			if($right >= $can)
				return true;
			
			return false;
		}
		
		return false;
	}
	elseif(!empty($media_catalogue_id))
	{
		if(!empty($rights[$media_catalogue_id]))
		{
			$permission = $rights[$media_catalogue_id];
			list($right, $width, $height) = explode(':', $permission);
			if($right > 0)
				return true;
			
			return false;
		}
		
		return false;
	}
	
	$rights = array_unique($rights);
	sort($rights);
	$max = array_pop($rights);
	list($right, $width, $height) = explode(':', $max);
	
	if($right > 0)
		return true;
	
	return false;
}
/* End of file media.php */
/* Location: ./application/helpers/media.php */