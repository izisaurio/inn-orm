<?php

namespace Inn\Exceptions;

use \Exception;

/**
 * Data type not found on database plugin exception
 *
 * @author	izisaurio
 * @version	1
 */
class PluginDataTypeNotFoundException extends Exception
{
	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	string	$type		Data type not found on dictionary
	 * @param	string	$plugin		Plugin where exception was thrown
	 */
	public function __construct($type, $plugin)
	{
		parent::__construct("Datatype ({$type}) not found on ({$plugin})");
	}
}
