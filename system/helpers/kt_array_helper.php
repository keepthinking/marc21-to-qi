<?php
/**
 * Get Attributes
 * Get the ID of all objects in an array
 * @param $arr stdClass[]
 * @param $attrib String
 * @return mixed[] whatever the attribute read is
 **/
function get_attributes(Array $arr, $attrib = 'id')
{
	$ids = array();
	foreach ($arr as $v)
	{
		if (is_array($v))
			$ids = array_merge($ids, get_attributes($v, $attrib));
		else
			$ids[] = $v->$attrib;
	}
	return $ids;
}

/**
 * Split Array
 * Split an array into pieces
 *
 * @param $list mixed[] The array to split up
 * @param $columns int The number of columns to split $list into
 * @author Sid Karunaratne
 * @return Array of arrays of length $columns
 * 
 **/
function split_array(Array $list, $columns)
{
	$per_column = ceil(count($list)/$columns);
	do
	{
		$split_list[] = array_splice($list, 0, $per_column);
	} while (--$columns > 0 && count($list) > 0);
	return $split_list;
}

	
/**
 * Index By
 * 
 * Takes an array of objects and indexes them by some key which is common to all the objects.
 * Does not alter ordering of objects. The resulting array will likely not have keys in ascending order
 * (say if you index by ID), but the order will be the same as previously to running this function
 * (say ordered by rank).
 * 
 * Though items can be lost when running this function if the key contains non-unique values
 * (say indexing by parent_id), only the first record with that key will be kept. If indexing
 * by parent_id, group_by should be used instead.
 * 
 * Version 2.0 - also accepting Arrays of Arrays instead of Arrays of Objects
 * 
 * @author Cristiano Bianchi
 * @param $records StdClass[]
 * @param $key String
 * @return StdClass[]
 */
function index_by($records, $key = 'id')
{
	$indexed = array();
	foreach ($records as $item)
	{
		if(is_array($item))
			$index = $item[$key];
		else
			$index = $item->$key;
		
		if (!array_key_exists($index, $indexed))
			$indexed[$index] = $item;
	}
	return $indexed;
}

/**
 * Group By
 * 
 * Takes an array of objects and indexes them by some key which is common to all the objects.
 * May alter ordering of objects, as records can be split into several groups. But the order
 * within a group will remain unchanged.
 * 
 * Each index will point to an array of records, not simply a record. This means that no records will be lost.
 * Useful for indexing by, say, parent_id, where the key is non-unique. If the key is known to be unique
 * then index_by should probably be used instead.
 * 
 * @author Sid Karunaratne
 * @param $records StdClass[]
 * @param $key String
 * @param $key_filter String - Regular expression filter for the key
 * @return Array[] An array of arrays of records, where the main array's indexes point to an array of items with that index as their key
 */
function group_by($records, $key = 'parent_id', $key_filter = null)
{
	$indexed = array();
	foreach ($records as $item)
	{
		if($key_filter)
			$group = preg_replace("/{$key_filter}/", '', $item->$key);
		else
			$group = $item->$key;
		if (!isset($indexed[$group]))
			$indexed[$group] = array();
		$indexed[$group][] = $item;
	}
	return $indexed;
}

/**
 * Recursive Count
 * 
 * Recursively counts the number of non-array objects in an array
 *
 * @author Sid Karunaratne
 * @param $arr mixed[]
 * @return int
 **/
function rcount(Array $arr)
{
	$count = 0;
	foreach ($arr as $item)
	{
		if (is_array($item))
			$count += rcount($item);
		else
			$count++;
	}
	return $count;
}

/**
* Flattens an array, or returns FALSE on fail - it is the opposite of index_by
*/
function array_flatten_children($array, $children_field = 'children') {
	$result = array();
	// Make a copy of the array first
	$array_copy = array();
	foreach($array as $value)
		$array_copy[] = clone $value;
	
	foreach ($array_copy as $key => $value) {
		if (!empty($value->$children_field)){
			$children = $value->$children_field;
			unset($value->$children_field);
			$result[] = $value;
			$result = array_merge($result, array_flatten_children($children));
		}
		else {
			$result[] = $value;
		}
	}
	return $result;
}

/**
 * Array has key
 * Check if an array has a key value that is not empty
 *
 * @param  type (bool,void, stdClass, array ect)
 * @param  type (bool,void, stdClass, array ect)
 * @author Richard Merchant
 * @return bool
 */
if (!function_exists('kt_array_has_key')) 
{
	function kt_array_has_key($items, $key)
	{
		if ( ! is_array($items))
		{
			$items = array($items);
		}
		
		foreach ($items as $item)
		{
			// make sure ite, is an array not an object
			$item = (array)$item;
			
			if (!empty($item[$key]))
			{
				return true;
			}
		}
			
		return false;
	}
}

/**
 * Search param value
 *  Return the searcj params value
 * 
 * @param $search_params search param values
 * @param $search_key  Search key
 * @return string/int
 * @author Richard Merchant
 **/
if (!function_exists('search_param_value')) 
{
	function search_param_value(Array $search_params, $search_key, $return_bool = false)
	{
		$return_value = '';

		if (!empty($search_params[$search_key][0]->values[0]))
		{
			$return_value = $return_value ? true : $search_params[$search_key][0]->values[0];
		}

		return $return_value;
	}
}

