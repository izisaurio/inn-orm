<?php

namespace Inn\Exceptions;

use \Exception;

/**
 * General exception thrown when something fails
 *
 * @author	izisaurio
 * @version	1
 */
class OrmException extends Exception
{
	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	string	$message		Error message
	 */
	public function __construct($message)
	{
		parent::__construct($message);
	}
}
