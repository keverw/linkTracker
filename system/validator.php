<?php
	function isEmail($str) //checks if the string is a valid email address
	{
		if (filter_var($str, FILTER_VALIDATE_EMAIL))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function isAlpha($str) //check if the string contains only letters (a-zA-Z).
	{
	    if (preg_match("/^[a-zA-Z]*$/",$str))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function isNumeric($str) //check if the string contains only numbers
	{
		if (is_int($str))
		{
			return true;
		}
		else if (is_string($str))
		{
			return ctype_digit($str);
		}
		else // booleans, floats and others
		{
			return false;
		}
	}
	
	function isAlphanumeric($str) //check if the string contains only letters and numbers
	{
		if (preg_match("/^[a-zA-Z0-9]*$/",$str))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
?>


