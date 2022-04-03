<?php

namespace Inn\Exceptions;

use \Exception;

/**
 * Exception thrown when trying to quote without Database
 *
 * @author	izisaurio
 * @version	1
 */
class DatabaseInstanceNotFoundException extends Exception
{
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct('The database instance to quote was not found');
	}
}
