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
	 * @param	array	$cases		Keys are cases, values are selected text
	 * @param	string	$column		Select column to be compared
	 * @param	string	$as			Select alias
	 * @param	string	$else		Else de los cases
	 */
	public function __construct(array $cases, $column = null, $else = null, $as = null)
	{
		$this->column = $column;
		$this->cases = $cases;
		$this->else = $else;
		$this->as = isset($as) ? $as : (isset($column) ? \str_replace('.', '_', $column) : 'cases');
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
				$key = !isset($this->column) ? $key : "'{$key}'";
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
		$column = isset($this->column) ? $this->column : '';
		return "CASE {$column} {$builded} END AS {$this->as}";
	}
}
