<?php

namespace Inn\Data;

use Inn\Exceptions\DatabaseStatementException;

/**
 * Database model, represents a row in a table
 *
 * @author	izisaurio
 * @version	1
 */
class DBModel extends Model
{
	/**
	 * Mapper that created the model
	 *
	 * @access	public
	 * @var		DBMapper
	 */
	public $_mapper;

	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	DBMapper	$mapper		Mapper that created the model
	 */
	public function __construct(DBMapper $mapper)
	{
		$this->_mapper = $mapper;
	}

	/**
	 * Returns an array with this model properties
	 *
	 * @access	public
	 * @return	array
	 */
	public function toArray()
	{
		$properties = \get_object_vars($this);
		unset($properties['_mapper']);
		return $properties;
	}

	/**
	 * Validates a model with the rules given
	 *
	 * @access	public
	 * @param	array			$rules		Model rules
	 * @param	array			$messages	Error messages array
	 * @throws	OrmException
	 * @return	Model
	 */
	public function validate(array $rules = null, array $messages = null)
	{
		if (!isset($rules)) {
			$rules = $this->_mapper->properties;
		}
		return parent::validate($rules, $messages);
	}

	/**
	 * Fill the model with default values given in the rules
	 *
	 * @access	public
	 * @param	array	$rules		Rules array
	 * @return	Model
	 */
	public function setDefaults(array $rules = null)
	{
		if (!isset($rules)) {
			$rules = $this->_mapper->properties;
		}
		return parent::setDefaults($rules);
	}

	/**
	 * Save model, insert of update if id is set
	 *
	 * @access	public
	 * @return	DBModel
	 * @throws	OrmException
	 */
	public function save()
	{
		try {
			$this->_mapper->save($this);
		} catch (DatabaseStatementException $ex) {
			throw new OrmException($ex->getMessage());
		}
		return $this;
	}

	/**
	 * Sets model id usinf last id
	 *
	 * @access	public
	 * @return	DBModel
	 */
	public function setInsertId()
	{
		$this->{$this->_mapper
			->primary} = $this->_mapper->database->connection->lastInsertId();
		return $this;
	}

	/**
	 * Deletes this model
	 *
	 * @access	public
	 * @throws	OrmException
	 */
	public function delete()
	{
		try {
			$this->_mapper->delete($this);
		} catch (DatabaseStatementException $ex) {
			throw new OrmException($ex->getMessage());
		}
	}

	/**
	 * Update foreign table with union data
	 *
	 * @access	public
	 * @param	DBUnion		$dbUnion	DBUnion instance to update
	 * @param	array		$data		Data to store
	 * @return	DBModel
	 * @throws	OrmException
	 */
	public function updateUnion(DBUnion $dbUnion, $data = [])
	{
		if (!$data || !is_array($data)) {
			$data = [];
		}
		try {
			$dbUnion->updateUnion($this, $data);
		} catch (DatabaseStatementException $ex) {
			throw new OrmException($ex->getMessage());
		}
		return $this;
	}

	/**
	 * Var dump model info, removes the mapper property
	 *
	 * @access	public
	 */
	public function __debugInfo()
	{
		return $this->toArray();
	}
}
