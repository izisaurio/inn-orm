<?php

namespace Inn\Data;

use \ReflectionClass,
	Inn\Sql\Sentence,
	Inn\Database\Database,
	Inn\Database\StatementParams;

/**
 * Database Union Table mapper class
 *
 * @author	izisaurio
 * @version	1
 */
class DBUnion extends Sentence
{
	/**
	 * Database instance
	 *
	 * @access	public
	 * @var		Database\Database
	 */
	public $database;

	/**
	 * Data of the two tables and their keys
	 *
	 * @access	public
	 * @var		array
	 */
	public $properties = [];

	/**
	 * Constructor
	 *
	 * Sets the database instance and the default table name if none
	 *
	 * @access	public
	 * @param	Database	$database	Database instance
	 */
	public function __construct(Database $database)
	{
		$this->database = $database;
		if (!isset($this->table)) {
			$this->table = (new ReflectionClass($this))->getShortName();
		}
	}

	/**
	 * Exceutes a union update
	 *
	 * @access	public
	 * @param	DBModel	$model		Model to update
	 * @param	array	$data		Values to register for the model
	 */
	public function updateUnion(DBModel $model, array $values)
	{
		$data = $this->getData($model);
		$this->where($data->this->key, $model->id);
		$this->database->execute($this->buildDelete());
		$this->database->prepareStatement(
			$this->buildInsert([$data->this->key, $data->union->key])
		);
		foreach ($values as $value) {
			$params = new StatementParams();
			$params->add('int', $model->id);
			$params->add('int', $value);
			$this->database->bindParams($params);
			$this->database->executeStatement();
		}
	}

	/**
	 * Returns all model related to the one given
	 *
	 * @access	public
	 * @param	DBModel	$model		Results will be related to this model
	 * @return	array
	 */
	public function findUnion(DBModel $model)
	{
		$data = $this->getData($model);
		$this->join(
			$this->table,
			$data->union->key,
			'=',
			"{$data->union->table}.{$data->union->id}"
		)->where($data->this->key, $model->id);
		return (new Result(
			$this->database->search($this->buildSelect()),
			$model->_mapper
		))->all();
	}

	/**
	 * Returns all model ids related to the one given
	 *
	 * @access	public
	 * @param	DBModel	$model		Results will be related to this model
	 * @return	array
	 */
	public function findUnionIds(DBModel $model)
	{
		$data = $this->getData($model);
		$this->select([$data->union->key])->where($data->this->key, $model->id);
		return (new Result(
			$this->database->search($this->buildSelect()),
			$model->_mapper
		))->column();
	}

	/**
	 * Searches this union raw data
	 *
	 * @access	public
	 * @return	array
	 */
	public function findAll()
	{
		return $this->database->search($this->select(['*'])->buildSelect());
	}

	/**
	 * Deletes a single value from this union
	 *
	 * @access	public
	 * @param	DBModel	$model		A value will be deleted from this model
	 * @param	mixed	$value		Value to delete
	 */
	public function deleteValue(DBModel $model, $value)
	{
		$data = $this->getData($model);
		$this->where($data->this->key, $model->id)->where(
			$data->union->key,
			$value
		);
		$this->database->execute($this->buildDelete());
	}

	/**
	 * Delete rows on current mapper conditionals
	 *
	 * @param	?StatementParams	$params		Params to add to sentence
	 * @access	public
	 */
	public function deleteAll(?StatementParams $params = null)
	{
		$this->database->execute($this->buildDelete(), $params);
	}

	/**
	 * Retunrs ordered properties
	 *
	 * @access	protected
	 * @param	DBModel	$model		Model to use
	 * @return	\stdClass
	 */
	protected function getData(DBModel $model)
	{
		$table = $model->_mapper->table;
		foreach ($this->properties as $key => $values) {
			if ($key != $table) {
				return (object) [
					'this' => (object) [
						'table' => $table,
						'key' => $this->properties[$table]['key'],
						'id' => $this->properties[$table]['id'],
					],
					'union' => (object) [
						'table' => isset($values['table'])
							? $values['table']
							: $key,
						'key' => $values['key'],
						'id' => $values['id'],
					],
				];
			}
		}
	}
}
