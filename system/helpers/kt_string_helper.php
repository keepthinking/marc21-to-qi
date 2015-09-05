<?php

if ( ! function_exists('pluralize'))
{
	function pluralize($count, $singular, $suffix = false, $plural = false)
	{
		$singular = lang($singular);
		if (!$plural)
			$plural = $singular . 's';
		else
			$plural = lang($plural);
		
		return ($count == 1 ? $singular : $plural) . ($suffix && $count > 1 ? ' (' . $count . ')' : '');
	}
}

// check for a paragraph in a string - adds one if none is found
function check_paragraphs($text) 
{
	return stripos($text, "<p>") !== FALSE ? $text : '<p>' . $text . '</p>';
}

function check_copyright($text) 
{
	return str_ireplace(array('©', '&copy;'), '', $text);
}

/**
 * Static Content URL
 * 
 * In order to increase the number of pipelined requests we put static content on domains such as static1.example.com and static2.example.com
 * This lets the browser pipeline more requests per time, but since we also want items to be cached well we can't simply use a random one each time
 * On the other hand we should use the domains in a way that causes the load to be fairly shared among them.
 * 
 * This function uses a hashing algorithm on the original url of the static item to get a psuedo-random seed which it then uses to pick a random domain
 * from the list in the config file
 * 
 * @author Sid Karunaratne
 * @param $url String The full URL of the static content (including http://)
 * @return String On success a string of the new URL is returned, otherwise the old URL is returned
 */
function static_content_url($url)
{
	$domains = config('static_content_domains');
	$protocol = substr($url, 0, strpos($url, '://'));
	if (!array_key_exists($protocol, $domains))
		return $url;
	$domains = $domains[$protocol];
	$seed = md5($url);
	$seed = $seed[0];
	$domain = $domains[floor((hexdec($seed)/16)*count($domains))];
	// Remove the protocol and the domain from the current url
	$url = substr($url, strpos($url, '/', strlen($protocol)+3));

	// Now insert our domain into the url
	return $protocol . '://' . $domain . $url;
}

/**
 * Format Size
 * 
 * @author php@wormss.net from http://uk.php.net/filesize
 * @param $size int
 * @param $round int
 * @return String
 */
function format_size($size, $round = 0)
{
	//Size must be bytes!
	$sizes = array('B', 'KB', 'MB', 'GB');
	for ($i=0; $size > 1024 && $i < count($sizes) - 1; $i++)
		$size /= 1024;
	return round($size,$round) . ' ' . $sizes[$i];
}

/**
 * Pick
 * Duplicate of MooTool's pick function, return the first argument not equivalent to false
 * Note that you may have to use a lot of shut-up operators (the @ sign) when calling this if passing $array[$index] when the index may or may not exist
 * Example: $per_page = pick($url_parameters->per_page, get_cookie("per_page"), 25);
 * @param mixed, mixed, ...., n
 * @return mixed the first of the non-false arguments, note that null, false, an empty string, the number 0 are all false values
 */
function pick()
{
	foreach (func_get_args() as $arg)
		if ((is_string($arg) && strlen($arg) > 0) || $arg)
			return $arg;
	return false;
}

/**
 * Escape
 * Removes HTML tags & garbage data. Converts HTML entities into text.
 *
 * @param $str The string
 * @author Parahat Melayev
 * @return string
 */
function esc($str)
{
	//Remove HTML
	// $str = strip_tags($str);
	//Remove bad UTF-8 data
	$str = preg_replace('/([\x00-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3})|./x', '$1', $str);
	//convert html entities
	$str = html_entity_decode($str, ENT_QUOTES, "UTF-8"); //revert previous html entities
	$str = htmlentities($str, ENT_QUOTES, "UTF-8");
	return $str;
}


/**
 * Associative URL
 * Lets you create part of a URL structure which uses associated keys
 * Lets say you are currently on /artist_id/123 and you want to go to a specific work with id 456 within the context of the artist you pass:
 * array('artist_id' => 123) as old_parameters and array("work_id" => 456) as new_parameters
 * Say that you want to remove the artist from the URL (ie go back to the artist listing) you pass the same old_parameters but array('artist_id' => null) as new_parameters
 * any array values equivalent to false will be printed as a 0 with the exception of null, which removes that component from the url.
 * @param $old_parameters mixed string[] or stdClass The old parameters, eg the base url
 * @param $new_parameters string[] The new parameters, eg what to append/remove
 * @return string
 */
function assoc_url($old_parameters, $new_parameters = array())
{
	if (!is_array($old_parameters))
		$old_parameters = (array) $old_parameters;
	$p = array_merge($old_parameters, $new_parameters);
	$url = "";
	// The following could be done with lambda functions or create_function, but the former is PHP 5.3+ only and the latter is ugly. Since using version_compare means maintaining two sets of code we'll use the backwards-compatible code
	foreach ($p as $key => $value)
	{
		if (is_null($value))
			continue;
		$value = pick($value, "0");
		$url .= "{$key}/{$value}/";
	}
	return $url;
}

/**
 * kt_currency
 * Formats number for use as currency
 */
if(!function_exists('kt_currency'))
{
	function kt_currency($value, $add_symbol = 1)
	{
		if($value !== null && is_numeric($value))
		{
			$thousand_separator = pick(config('thousand_separator'), ",");
			$decimal_separator = pick(config('decimal_separator'), ".");
			$decimal_places = pick(config('decimal_places'), 0);
			$currency_symbol = pick(config('currency_symbol'), "£");
			
			$number = explode(".", $value);
			if(!$add_symbol && (empty($number[1]) || ( !empty($number[1]) && intval($number[1]) == 0)))
				$decimal_places = 0;
			
			if(!empty($add_symbol))
				return $currency_symbol . number_format ($value, $decimal_places, $decimal_separator, $thousand_separator);
			else
				return number_format ($value, $decimal_places, $decimal_separator, $thousand_separator);
		}
		return null;
	}
}
