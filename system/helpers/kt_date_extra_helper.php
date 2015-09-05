<?php
/**
 * Format date in human terms
 * @author Parahat Melayev
 * @return void
**/
if( ! function_exists('date_human') )
{
	function date_human($input, $format = 'j F Y')
	{
		$input = strtotime($input);
		return date($format, $input);
	}
}


if( ! function_exists('date_range_human') )
{
	function date_range_human($start_date, $end_date, $format = 'j F Y')
	{
		// Convert to timestamp
		$start_date = strtotime($start_date);
		
		// No end date
		if(!$end_date)
			return date($format, $start_date);
		
		$end_date = strtotime($end_date);
		
		// Start date in the past and end date in the future
		if($start_date < date('U') && $end_date > date('U'))
			return "Until " . date($format, $end_date);
		
		// Same year and month
		if(date('Y-m', $start_date) == date('Y-m', $end_date))
			return date('j', $start_date) . ' to '. date($format, $end_date);
		
		// Same year, different month
		if(date('Y', $start_date) === date('Y', $end_date))
			return date('j F', $start_date) . ' to '. date($format, $end_date);
			
		// Different years
			return date($format, $start_date) . ' to '. date($format, $end_date);
	}
}

if( ! function_exists('relative_time') )
{
	function relative_time($timestamp)
	{
		$difference = time() - $timestamp;
		$periods = array("sec", "min", "hour", "day", "week",
			"month", "years", "decade");
		$lengths = array("60","60","24","7","4.35","12","10");

		if ($difference > 0) { // this was in the past
			$ending = "ago";
		} else { // this was in the future
			$difference = -$difference;
			$ending = "to go";
		}
		
		for($j = 0; $difference >= $lengths[$j]; $j++)
			$difference /= $lengths[$j];

		if ($difference < 1)
			return "just now";

		$difference = round($difference);
		if($difference != 1) $periods[$j].= "s";
		$text = "{$difference} {$periods[$j]} {$ending}";
		return $text;
	}
}

