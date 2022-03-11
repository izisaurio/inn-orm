<?php

namespace Inn\Exceptions;

use \Exception;

/**
 * Property not found on model exception
 *
 * @author	izisaurio
 * @version	1
 */
class PropertyNotFoundException extends Exception
{
	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	string	$table			Model class or table
	 * @param	string	$property		Property or column name
	 */
	public function __construct($table, $property)
	{
		parent::__construct("Field not found ({$table}->{$property})");
	}
}
