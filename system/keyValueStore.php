<?php
	function kvs_read($key)
	{
		global $db;
		
		$info = array(
			'dbErr' => false,
			'found' => false,
			'value' => ''
		);
		
		if ($result = $db->select("SELECT kvs_value FROM options WHERE kvs_key = '%s' LIMIT 1", $key))
		{
			if ($db->numRows > 0)
			{
				$info['found'] = true;
				$info['value'] = $result[0]['kvs_value'];
			}
			else
			{
				$info['found'] = false;
			}
			
		}
		else //database error
		{
			$info['dbErr'] = true;
		}

		return $info;
	}
	
	function kvs_write($key, $value)
	{
		global $db;
		
		if ($result = $db->replace('options', array('kvs_key' => $key, 'kvs_value' => $value)))
		{
			return true;
		}
		else
		{
			return false;
		}
		
	}
	
	function kvs_delete($key)
	{
		global $db;
		
		if ($result = $db->delete('options', "kvs_key = '%s'", $key))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
?>