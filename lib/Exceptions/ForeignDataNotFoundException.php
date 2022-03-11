<?php

namespace Inn\Exceptions;

use \Exception;

/**
 * Exception thrown when a foreign column data is not found in the mapper properties
 *
 * @author	izisaurio
 * @version	1
 */
class ForeignDataNotFoundException extends Exception
{
	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	string	$table	Mapper table name throwing the error
	 * @param	string	$key	Mapper key to foreign table
	 */
	public function __construct($table, $key)
	{
		parent::__construct(
			"Foreign data not found in ({$table}) with key ({$key})"
		);
	}
}
