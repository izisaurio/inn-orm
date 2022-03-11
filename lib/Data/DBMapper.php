<?php

namespace Inn\Data;

use \ReflectionClass,
	Inn\Sql\Sentence,
	Inn\Sql\ForeignColumns,
	Inn\Database\Database,
	Inn\Database\StatementParams;

/**
 * Database Table mapper class
 *
 * @author	izisaurio
 * @version	1
 */
class DBMapper extends Sentence
{
	/**
	 * Database instance
	 *
	 * @access	public
	 * @var		Database\Database
	 */
	public $database;

	/**
	 * Id column name
	 *
	 * @access	public
	 * @var		string
	 */
	public $primary = 'id';

	/**
	 * Table properties with validations
	 *
	 * @access	public
	 * @var		array
	 */
	public $properties = [];

	/**
	 * Contruct
	 *
	 * Sets the database instance and the default table name if none
	 *
	 * @access	public
	 * @param	Database\Database	$database	Database instance
	 */
	public function __construct(Database $database)
	{
		$this->database = $database;
		if (!isset($this->table)) {
			$this->table = (new ReflectionClass($this))->getShortName();
		}
	}

	/**
	 * Select table columns
	 *
	 * @access	public
	 * @param	array		$select		Columns
	 * @return	mixed
	 */
	public function select(array $select)
	{
		$columns = [];
		foreach ($select as $key => $column) {
			if (\is_string($key)) {
				if (!isset($this->properties[$key]['union'])) {
					throw new ForeignDataNotFoundException($this->table, $key);
				}
				$columns[] = new ForeignColumns(
					$key,
					$column,
					$this->properties[$key]['union']
				);
				continue;
			}
			$columns[] = $column;
		}
		return parent::select($columns);
	}

	/**
	 * Returns a search query Result object
	 *
	 * @access	public
	 * @param	Database\StatementParams	$params		Params to add to search sentence
	 * @return	Result
	 */
	public function find(StatementParams $params = null)
	{
		if (empty($this->select)) {
			$this->select(['*']);
		}
		return new Result(
			$this->database->search($this->buildSelect(), $params),
			$this
		);
	}

	/**
	 * Returns a single model by id column
	 *
	 * @access	public
	 * @param	mixed	$id		Column id
	 * @return	DBModel
	 */
	public function findId($id)
	{
		return $this->where($this->primary, $id)
			->limit(1)
			->find()
			->first();
	}

	/**
	 * Inserts a model to database
	 *
	 * @access	public
	 * @param	Data\DBModel	$model		Model to insert
	 * @throws	PropertyNotFoundException
	 */
	public function insert(DBModel $model)
	{
		$values = $model->toArray();
		$params = new StatementParams();
		foreach ($this->properties as $property => $options) {
			if (
				!\array_key_exists($property, $values) &&
				$property != $this->primary
			) {
				throw new PropertyNotFoundException($this->table, $property);
			}
			$type = \is_array($options) ? $options['type'] : $options;
			$params->add($type, $values[$property]);
		}
		$this->database->execute(
			$this->buildInsert(\array_keys($this->properties)),
			$params
		);
	}

	/**
	 * Updates values of current mapper conditionals
	 *
	 * @access	public
	 * @param	array	$data	Data to update
	 */
	public function updateAll(array $data)
	{
		$properties = \array_intersect_key($this->properties, $data);
		$params = new StatementParams();
		foreach ($properties as $key => $property) {
			$type = \is_array($property) ? $property['type'] : $property;
			$params->add($type, $data[$key]);
		}
		$this->database->execute(
			$this->buildUpdate(\array_keys($properties)),
			$params
		);
	}

	/**
	 * Updates a model
	 *
	 * @access	public
	 * @param	Data\DBModel	$model	Model to update
	 */
	public function update(DBModel $model)
	{
		$data = $model->toArray();
		if (!isset($data[$this->primary])) {
			throw new PropertyNotFoundException($this->table, $this->primary);
		}
		$this->where = [];
		$this->where($this->primary, '=', $data[$this->primary]);
		$this->updateAll($model->toArray());
	}

	/**
	 * Saves a model, insert or update if id is present
	 *
	 * @access	public
	 * @param	Data\DBModel	$model		Model to save
	 * @param	bool	$lastId		Set last id to model flag when insertion
	 */
	public function save(DBModel $model)
	{
		if (isset($model->{$this->primary})) {
			$this->update($model);
		} else {
			$this->insert($model);
		}
	}

	/**
	 * Delete rows on current mapper conditionals
	 *
	 * @param	Database\StatementParams	$params		Params to add to sentence
	 * @access	public
	 */
	public function deleteAll(StatementParams $params = null)
	{
		$this->database->execute($this->buildDelete(), $params);
	}

	/**
	 * Deletes a model row
	 *
	 * @access	public
	 * @param	Data\DBModel	$model	Model to delete
	 */
	public function delete(DBModel $model)
	{
		$this->where('id', '=', '?');
		$params = new StatementParams(['INT' => $model->{$this->primary}]);
		$this->deleteAll($params);
	}

	/**
	 * Counts results of current mapper conditionals
	 *
	 * @access	public
	 * @param	Database\StatementParams	$params		Params to add to sentence
	 * @return	int
	 */
	public function count(StatementParams $params = null)
	{
		$select = empty($this->groupBy)
			? 'COUNT(1) AS count'
			: "COUNT(DISTINCT {$this->table}.{$this->primary}) AS count";
		$counted = $this->select([$select])
			->limit(1)
			->find($params)
			->first();
		return isset($counted) ? $counted->count : 0;
	}

	/**
	 * Returns if a current mappers conditionals exist
	 *
	 * @access	public
	 * @param	Database\StatementParams	$params		Params to add to sentence
	 * @return	bool
	 */
	public function exists(StatementParams $params = null)
	{
		$select = $this->select([1])->buildSelect();
		$exists = "SELECT EXISTS ({$select}) AS FOUND";
		return (new Result(
			$this->database->search($exists, $params),
			$this
		))->source()[0]['FOUND'];
	}

	/**
	 * Returns a new instance with same table and Database instance
	 *
	 * @access	protected
	 * @return	DBMapper
	 */
	protected function newSelf()
	{
		return new static($this->database);
	}

	/**
	 * Returns a new DBModel with this mapper set
	 *
	 * @access	public
	 * @return	DBMapper
	 */
	public function getModel()
	{
		return new DBModel($this);
	}
}
