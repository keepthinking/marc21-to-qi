<?php
/**
 * Function for debugging
 * @author Parahat Melayev
 * @return void
**/
if( ! function_exists('debug') )
{
	function debug($input)
	{
		$print = false;
		if(defined('ENVIRONMENT') && ENVIRONMENT == 'development')
			$print = true;
		if(defined('LIVE') && LIVE === false)
			$print = true;
			
		if($print)
		{
			$backtrace = debug_backtrace();
			$out  = '<pre>';
			$out .= 'FILE: '. $backtrace[0]['file'] . PHP_EOL .
					'LINE: '. $backtrace[0]['line'] . PHP_EOL .
					'CALL: ';
			foreach($backtrace as $b)
				$out .= (!empty($b['class'])?$b['class'].'::'.$b['function']:$b['function']) . ' < ' . @$b['line'] .':';
			$out .=  PHP_EOL;
			$out .=  print_r($input, true) . 
					 '</pre> -----------'; 
			echo $out;
		}
	}
}
