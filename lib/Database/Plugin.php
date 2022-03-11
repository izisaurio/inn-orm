<?php

namespace Inn\Database;

/**
 * PDO connection plugin base
 *
 * @author	izisaurio
 * @version	1
 */
abstract class Plugin
{
	/**
	 * Data source name
	 *
	 * @access	public
	 * @param	string
	 */
	public $dsn;

	/**
	 * Database user name
	 *
	 * @access	public
	 * @param	string
	 */
	public $user;

	/**
	 * Database user password
	 *
	 * @access	public
	 * @param	string
	 */
	public $password;

	/**
	 * Database charset
	 *
	 * @access	public
	 * @param	string
	 */
	public $charset;

	/**
	 * Returns the specific plugin Statement paramater data type
	 *
	 * @access	public
	 * @param	string	$type	Innsert\orm type
	 * @return	int
	 */
	abstract public function translate($type);
}
