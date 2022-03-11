<?php

namespace Inn\Data;

/**
 * Database table mapper search result manager
 *
 * @author	izisaurio
 * @version	1
 */
class Result
{
	/**
	 * Results form query
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $source;

	/**
	 * Mapper that created the results
	 *
	 * @access	protected
	 * @var		Data\DBMapper
	 */
	protected $mapper;

	/**
	 * Constructor
	 *
	 * Sets source and mapper
	 *
	 * @access	public
	 * @param	array			$source		Query results raw
	 * @param	Data\DBMapper	$mapper		Mapper that created  the results
	 */
	public function __construct(array $source, DBMapper $mapper)
	{
		$this->source = $source;
		$this->mapper = $mapper;
	}

	/**
	 * Returns results source
	 *
	 * @access	public
	 * @return	array
	 */
	public function source()
	{
		return $this->source;
	}

	/**
	 * Returns results as a model collection
	 *
	 * @access	public
	 * @return	array
	 */
	public function all()
	{
		$results = [];
		foreach ($this->source as $row) {
			$model = $this->mapper->getModel();
			foreach ($row as $property => $value) {
				$model->$property = $value;
			}
			$results[] = $model;
		}
		return $results;
	}

	/**
	 * Returns a single column
	 *
	 * @access	public
	 * @return	array
	 */
	public function column()
	{
		if (empty($this->source)) {
			return [];
		}
		$column = \array_key_first($this->source[0]);
		return \array_column($this->source, $column);
	}

	/**
	 * Returns first to columns as an associative array
	 *
	 * @access	public
	 * @return	array
	 */
	public function assoc()
	{
		if (empty($this->source)) {
			return [];
		}
		list($key, $value) = \array_keys($this->source[0]);
		return \array_column($this->source, $value, $key);
	}

	/**
	 * Returns first result as model
	 *
	 * @access	public
	 * @return	Data\DBModel
	 */
	public function first()
	{
		$all = $this->all();
		return isset($all[0]) ? $all[0] : null;
	}
}
