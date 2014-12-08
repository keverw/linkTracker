<?php
	function h($str)
	{
		return htmlentities($str);
	}
	
	function redirect($url)
	{
		header('Location: ' . $url);
		die();
	}
	
	function getCurrentDBVersion()
	{
		return 1;
	}
	
	function writeEventLog(&$pdbh, $user, $msg)
	{
		if ($result = $pdbh->insert('eventLog', array(
			'user' => $user,
			'ip' => $_SERVER['REMOTE_ADDR'],
			'date' => time(),
			'msg' => $msg
		)))
		{
			return true;
		}
		else
		{
			return false;
		}

	}
	
	function cleanMySQLVersion($ver)
	{
		//brrowed from YOURLS
		/**
		* The regex removes everything that's not a number at the start of the string, or remove anything that's not a number and what
		* follows after that.
		*   'omgmysql-5.5-ubuntu-4.20' => '5.5'
		*   'mysql5.5-ubuntu-4.20'     => '5.5'
		*   '5.5-ubuntu-4.20'          => '5.5'
		*   '5.5-beta2'                => '5.5'
		*   '5.5'                      => '5.5'
		*/
		
		return preg_replace( '/(^[^0-9]*)|[^0-9.].*/', '', $ver);
	}
	
?>