<?php
function config($item)
{
	$obj =& get_instance();
	return $obj->config->item($item);
}

function compressed($compress_method = '')
{
	if ($compress_method === 'compiled'){
		return (LIVE ? '.compiled' : '');
	} else {
		return (LIVE ? '.min' : '');
	}
}
?>