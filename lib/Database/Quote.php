<?php

namespace Inn\Database;

use Inn\Exceptions\DatabaseInstanceNotFoundException;

/**
 * Quotes a value with the provided Database instance
 *
 * @author	izisaurio
 * @version	1
 */
class Quote
{
	/**
	 * Database
	 *
	 * @static
	 * @access	private
	 * @var		Database
	 */
	private static $database;

	/**
	 * Value to quote
	 *
	 * @access	public
	 * @var		mixed
	 */
	public $value;

	/**
	 * Construct
	 *
	 * Receives the value to quote
	 *
	 * @access	public
	 * @param	mixed	$value	Value to quote
	 * @throws	DatabseInstanceNotFoundException
	 */
	public function __construct($value)
	{
		if (!isset(self::$database)) {
			throw new DatabaseInstanceNotFoundException();
		}
		if (!isset($value)) {
			$value = '';
		}
		$this->value = is_array($value)
			? \array_map([self::$database->connection, 'quote'], $value)
			: self::$database->connection->quote($value);
	}

	/**
	 * Sets the Database instance
	 *
	 * @static
	 * @access	public
	 * @param	Database	$database	The database instance
	 */
	public static function setDatabase(Database $database)
	{
		self::$database = $database;
	}

	/**
	 * To String
	 *
	 * Returns the quoted value
	 *
	 * @access	public
	 * @return	string
	 */
	public function __toString()
	{
		return \is_array($this->value)
			? \join(',', $this->value)
			: $this->value;
	}
}
