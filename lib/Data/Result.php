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
	 * @var		DBMapper
	 */
	protected $mapper;

	/**
	 * Json values to decode
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $decode = [];

	/**
	 * When json decoded set it as array
	 *
	 * @access	private
	 * @var		bool
	 */
	private $decodeAsArray = true;

	/**
	 * Constructor
	 *
	 * Sets source and mapper
	 *
	 * @access	public
	 * @param	array		$source		Query results raw
	 * @param	DBMapper	$mapper		Mapper that created  the results
	 */
	public function __construct(array $source, DBMapper $mapper)
	{
		$this->source = $source;
		$this->mapper = $mapper;
	}

	/**
	 * Sets decode flag to true
	 *
	 * @access	public
	 * @param	array	$columns	Columns to decode
	 * @param	bool	$asArray	Flag to decode as array
	 * @return	Result
	 */
	public function decode(array $columns, $asArray = false)
	{
		$this->decodeAsArray = $asArray;
		$this->decode = $columns;
		return $this;
	}

	/**
	 * Returns results source
	 *
	 * @access	public
	 * @return	array
	 */
	public function source()
	{
		if (!empty($this->decode)) {
			foreach ($this->source as &$item) {
				foreach ($this->decode as $column) {
					if (!isset($item[$column])) {
						continue;
					}
					$json = \json_decode($item[$column], $this->decodeAsArray);
					if (\json_last_error() !== JSON_ERROR_NONE) {
						$item[$column] = null;
						continue;
					}
					$item[$column] = $json;
				}
			}
		}
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
		$source = $this->source();
		foreach ($source as $row) {
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
		return \array_column($this->source(), $column);
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
		return \array_column($this->source(), $value, $key);
	}

	/**
	 * Returns model collection or source using a field as a key
	 *
	 * @access	public
	 * @param	string	$key	Field name to use as key
	 * @param	bool	$source	Flag to return source instead of models
	 * @return	array
	 */
	public function indexed($key, $source = false)
	{
		if (empty($this->source)) {
			return [];
		}
		$keys = \array_column($this->source, $key);
		$collection = $source ? $this->source() : $this->all();
		return \array_combine($keys, $collection);
	}

	/**
	 * Returns source using a field as a key
	 *
	 * @access	public
	 * @param	string	$key	Field name to use as key
	 * @return	array
	 */
	public function indexedSource($key)
	{
		return $this->indexed($key, true);
	}

	/**
	 * Returns first result as model
	 *
	 * @access	public
	 * @return	DBModel
	 */
	public function first()
	{
		$all = $this->all();
		return isset($all[0]) ? $all[0] : null;
	}
}
