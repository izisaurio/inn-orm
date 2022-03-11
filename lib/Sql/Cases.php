<?php

namespace Inn\Sql;

/**
 * Sql Sentence cases for a select
 *
 * @author	izisaurio
 * @version	1
 */
class Cases
{
	/**
	 * Column to search on sentence
	 *
	 * @access	public
	 * @var		string
	 */
	public $column;

	/**
	 * Alias of selected field on sentence
	 *
	 * @access	public
	 * @var		string
	 */
	public $as;

	/**
	 * Collection with cases to select
	 *
	 * @access	public
	 * @var		array
	 */
	public $cases;

	/**
	 * Optional else if no cases match
	 *
	 * @access	public
	 * @var		string
	 */
	public $else;

	/**
	 * Constructor
	 *
	 * Sets source and mapper
	 *
	 * @access	public
	 * @param	string	$column		Select column to be compared
	 * @param	string	$as			Select alias
	 * @param	array	$cases		Keys are cases, values are selected text
	 * @param	string	$else		Else de los cases
	 */
	public function __construct($column, $as, array $cases, $else = null)
	{
		$this->column = $column;
		$this->as = $as;
		$this->cases = $cases;
		$this->else = $else;
	}

	/**
	 * Builds de cases syntax and returns it
	 *
	 * @access	public
	 * @return	string
	 */
	public function buildCases()
	{
		$builder = [];
		foreach ($this->cases as $key => $value) {
			if (\is_string($key)) {
				$key = "'{$key}'";
			}
			if (\is_string($value)) {
				$value = "'{$value}'";
			}
			$builder[] = "WHEN {$key} THEN {$value}";
		}
		if (isset($this->else)) {
			$builder[] = \is_string($this->else)
				? "ELSE '{$this->else}'"
				: "ELSE {$this->else}";
		}
		$builded = \join(' ', $builder);
		return "CASE {$this->column} {$builded} END AS {$this->as}";
	}
}
