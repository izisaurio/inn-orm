<?php

namespace Inn\Exceptions;

use \Exception;

/**
 * Exception when executing a database statement
 *
 * @author	izisaurio
 * @version	1
 */
class DatabaseStatementException extends Exception
{
	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	string	$message	Error message
	 * @param	string	$query		Sql sentence executing
	 */
	public function __construct($message, $query)
	{
		parent::__construct(
			"Error in sentence (Err: {$message}) - (Query: {$query})"
		);
	}
}
