<?php

namespace Inn\Sql;

/**
 * Column(s) to select when shortcut selected
 *
 * @author	izisaurio
 * @version	1
 */
class ForeignColumns
{
	/**
	 * Foreign table
	 *
	 * @access	public
	 * @var		string
	 */
	public $table;

	/**
	 * Foreign table key
	 *
	 * @access	public
	 * @var		string
	 */
	public $foreignKey;

	/**
	 * Current table key
	 *
	 * @access	public
	 * @var		string
	 */
	public $tableKey;

	/**
	 * Foreign table columns to select
	 *
	 * @access	public
	 * @var		array
	 */
	public $columns;

	/**
	 * Construct
	 *
	 * Sets the initial values
	 *
	 * @access	public
	 * @param	string			$foreign	Foregin table union data
	 * @param	string|array	$columns	Foregin table columns
	 * @param	string			$data		Foreign union data
	 */
	public function __construct($foreign, $columns, $data)
	{
		list($table, $key) = \explode('|', $data);
		$this->table = $table;
		$this->foreignKey = $key;
		$this->tableKey = $foreign;
		$selects = \is_array($columns) ? $columns : [$columns];
		$this->columns = \array_map([$this, 'columnWithTable'], $selects);
	}

	/**
	 * Sets respective table name to columns if none given
	 *
	 * @access	protected
	 * @param	string		$column		Column name
	 */
	protected function columnWithTable($column)
	{
		return \strpos($column, ' ') === false
			? "{$this->table}.{$column} AS {$this->tableKey}_{$column}"
			: "{$this->table}.{$column}";
	}
}
