<?php
	session_start();

	$auto_save = array('country', 'language', 'b_code', 'book_index', 'chapter_index');

	foreach ($auto_save as $item)
	{
		if(isset($_COOKIE[$item]))
		{
			$$item = $_COOKIE[$item];
		}
	}


?>