<?php

namespace Inn\Exceptions;

use \Exception;

/**
 * Exception thrown when failing to connect to database
 *
 * @author	izisaurio
 * @version	1
 */
class DatabaseConnectException extends Exception
{
	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	int		$code	Error code
	 */
	public function __construct($code)
	{
		parent::__construct(
			"A connection could not be established to the database (Err: {$code})"
		);
	}
}
