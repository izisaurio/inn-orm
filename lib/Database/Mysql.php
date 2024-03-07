<?php

namespace Inn\Database;

use \PDO, Inn\Exceptions\PluginDataTypeNotFoundException;

/**
 * PDO connection plugin for mysql connections
 *
 * @author	izisaurio
 * @version	1
 */
class Mysql extends Plugin
{
	/**
	 * PDO Data type dictionary
	 *
	 * @access	public
	 * @var		array
	 */
	public $dictionary = [
		'int' => PDO::PARAM_INT,
		'decimal' => PDO::PARAM_STR,
		'string' => PDO::PARAM_STR,
		'text' => PDO::PARAM_STR,
		'datetime' => PDO::PARAM_STR,
		'date' => PDO::PARAM_STR,
		'time' => PDO::PARAM_STR,
		'timestamp' => PDO::PARAM_INT,
		'bool' => PDO::PARAM_BOOL,
		'json' => PDO::PARAM_STR,
	];

	/**
	 * Construct
	 *
	 * Gets the database connection data
	 *
	 * @access	public
	 * @param	string	$server		Database host
	 * @param	string	$database	Database name
	 * @param	string	$user		Database username
	 * @param	string	$password	Database user password
	 * @param	string	$posrt		Database port
	 * @param	string	$charset	Database default charset
	 */
	public function __construct(
		$server,
		$database,
		$user,
		$password,
		$port = 3306,
		$charset = 'utf8mb4'
	) {
		$this->dsn = "mysql:dbname={$database};host={$server};port={$port}";
		$this->user = $user;
		$this->password = $password;
		$this->charset = $charset;
	}

	/**
	 * Returns the pdo data type of a innsert\orm type
	 *
	 * @access	public
	 * @param	string		$type	Innsert\orm type
	 * @return	int
	 * @throws	PluginDataTypeNotFoundException
	 */
	public function translate($type)
	{
		if (!\array_key_exists($type, $this->dictionary)) {
			throw new PluginDataTypeNotFoundException($type, 'MySql');
		}
		return $this->dictionary[$type];
	}
}
