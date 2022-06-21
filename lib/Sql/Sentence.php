<?php

namespace Inn\Sql;

use \Closure, Inn\Database\Quote;

/**
 * Sql sentence builder
 *
 * @author	izisaurio
 * @version	1
 */
class Sentence
{
	/**
	 * Table name
	 *
	 * @access	public
	 * @var		string
	 */
	public $table;

	/**
	 * Collection of columns
	 *
	 * @access	public
	 * @var		array
	 */
	public $select = [];

	/**
	 * Collection of conditionals
	 *
	 * @access	public
	 * @var		array
	 */
	public $where = [];

	/**
	 * Collection joins
	 *
	 * @access	public
	 * @var		array
	 */
	public $join = [];

	/**
	 * Collection of conditionals for a join
	 *
	 * @access	public
	 * @var		array
	 */
	public $on = [];

	/**
	 * Collection order conditions
	 *
	 * @access	public
	 * @var		array
	 */
	public $orderBy = [];

	/**
	 * Collection of group conditions
	 *
	 * @access	public
	 * @var		array
	 */
	public $groupBy = [];

	/**
	 * Collection of having conditions
	 *
	 * @access	public
	 * @var		array
	 */
	public $having = [];

	/**
	 * Query result limit
	 *
	 * @access	public
	 * @var		string
	 */
	public $limit = '';

	/**
	 * Index in search query
	 *
	 * @access	public
	 * @var		string
	 */
	public $index = '';

	/**
	 * Select alias of sub sentence
	 *
	 * @access	public
	 * @var		string
	 */
	public $alias;

	/**
	 * Select table columns
	 *
	 * @access	public
	 * @param	array		$select		Columns
	 * @return	mixed
	 */
	public function select(array $select)
	{
		foreach ($select as $column) {
			if (\is_string($column)) {
				$this->select[] =
					\strpos($column, '.') === false && \strpos($column, '(')
						? "{$this->table}.{$column}"
						: $column;
				continue;
			}
			if ($column instanceof ForeignColumns) {
				$this->join(
					$column->table,
					"{$column->table}.{$column->foreignKey}",
					'=',
					"{$this->table}.{$column->tableKey}"
				);
				foreach ($column->columns as $foreignColumn) {
					$this->select[] = $foreignColumn;
				}
				continue;
			}
			if ($column instanceof Sentence) {
				$subSentence = $column->buildSelect();
				if (!isset($column->alias)) {
					$column->alias = $column->table;
				}
				$this->select[] = "({$subSentence}) AS {$column->alias}";
				continue;
			}
			if ($column instanceof Cases) {
				$this->select[] = $column->buildCases();
				continue;
			}
			$this->select[] = $column;
		}
		return $this;
	}

	/**
	 * Sets alias name
	 *
	 * @access	public
	 * @param	string	$alias	Sentence alias
	 * @return	SubSentence
	 */
	public function alias($alias)
	{
		$this->alias = $alias;
		return $this;
	}

	/**
	 * Adds a raw where
	 *
	 * @access	private
	 * @param	string		$where		Where operation
	 * @param	string		$type		Where union type (and, or)
	 * @return	mixed
	 */
	private function rawWhere($where, $type = 'AND')
	{
		$where = empty($this->where) ? $where : "{$type} {$where}";
		$this->where[] = $where;
		return $this;
	}

	/**
	 * Add a where operation to sentence
	 *
	 * Add a closure as first param for nested wheres
	 *
	 * @access	public
	 * @param	string|Closure	$compare	Column or value to compare|Closure for nested wheres
	 * @param	mixed			$operator	Where operator
	 * @param	mixed			$to			Value to compare
	 * @param	string			$type		Where union type (and, or)
	 * @return	Sentence
	 */
	public function where($compare, $operator = null, $to = null, $type = 'AND')
	{
		if ($compare instanceof Closure) {
			$sentence = $this->newSelf();
			$compare($sentence);
			$where = \join(' ', $sentence->where);
			return $this->rawWhere("({$where})", $type);
		}
		if (!isset($to)) {
			$to = $operator;
			$operator = '=';
		}
		if (\is_string($to)) {
			$to = new Quote($to);
		}
		return $this->rawWhere("{$compare} {$operator} {$to}", $type);
	}

	/**
	 * Adds a where with "And" type
	 *
	 * Add a closure as first param for nested wheres
	 *
	 * @access	public
	 * @param	string|Closure	$compare	Column or value to compare|Closure for nested wheres
	 * @param	mixed			$operator	Where operator
	 * @param	mixed			$to			Value to compare
	 * @return	mixed
	 */
	public function andWhere($compare, $operator = null, $to = null)
	{
		return $this->where($compare, $operator, $to);
	}

	/**
	 * Adds a where with "Or" type
	 *
	 * Add a closure as first param for nested wheres
	 *
	 * @access	public
	 * @param	string|Closure	$compare	Column or value to compare|Closure for nested wheres
	 * @param	mixed			$operator	Where operator
	 * @param	mixed			$to			Value to compare
	 * @return	mixed
	 */
	public function orWhere($compare, $operator = null, $to = null)
	{
		return $this->where($compare, $operator, $to, 'OR');
	}

	/**
	 * Adds a where in to sentence
	 *
	 * @access	public
	 * @param	string			$compare	Column or value to compare
	 * @param	array|Quote		$values		Values
	 * @param	string			$type		Where union type (and, or)
	 * @return	mixed
	 */
	public function whereIn($compare, $values, $type = 'AND')
	{
		if (empty($values)) {
			return $this;
		}
		$data =
			$values instanceof Quote
				? \join(',', $values->value)
				: \join(',', $values);
		return $this->rawWhere("{$compare} IN ({$data})", $type);
	}

	/**
	 * Adss a "where in" to sentence of type "Or"
	 *
	 * @access	public
	 * @param	string			$compare	Column or value to compare
	 * @param	array|Quote		$values		Values
	 * @return	mixed
	 */
	public function orWhereIn($compare, array $values)
	{
		return $this->whereIn($compare, $values, 'OR');
	}

	/**
	 * Adds a "where not in" to sentence
	 *
	 * @access	public
	 * @param	string			$compare	Column or value to compare
	 * @param	array|Quote		$values		Values
	 * @param	string			$type		Where union type (and, or)
	 * @return	mixed
	 */
	public function whereNotIn($compare, array $values, $type = 'AND')
	{
		if (empty($values)) {
			return $this;
		}
		$data =
			$values instanceof Quote
				? \join(',', $values->value)
				: \join(',', $values);
		return $this->rawWhere("{$compare} NOT IN ({$data})", $type);
	}

	/**
	 * Adds a "where not in" to sentence of type "Or"
	 *
	 * @access	public
	 * @param	string			$compare	Column or value to compare
	 * @param	array|Quote		$values		Values
	 * @return	mixed
	 */
	public function orWhereNotIn($compare, $values)
	{
		return $this->whereNotIn($compare, $values, 'OR');
	}

	/**
	 * Adds a "where null" to sentence
	 *
	 * @access	public
	 * @param	string			$compare	Column or value to compare
	 * @param	string			$type		Where union type (and, or)
	 * @return	mixed
	 */
	public function whereNull($compare, $type = 'AND')
	{
		return $this->rawWhere("{$compare} IS NULL", $type);
	}

	/**
	 * Adds a "where null" to sentence of type "Or"
	 *
	 * @access	public
	 * @param	string			$compare	Column or value to compare
	 * @return	mixed
	 */
	public function orWhereNull($compare)
	{
		return $this->whereNull($compare, 'OR');
	}

	/**
	 * Adds a "where not null" to sentence
	 *
	 * @access	public
	 * @param	string			$compare	Column or value to compare
	 * @param	string			$type		Where union type (and, or)
	 * @return	mixed
	 */
	public function whereNotNull($compare, $type = 'AND')
	{
		return $this->rawWhere("{$compare} NOT NULL", $type);
	}

	/**
	 * Adds a "where not null" to sentence of type "Or"
	 *
	 * @access	public
	 * @param	string			$compare	Column or value to compare
	 * @return	mixed
	 */
	public function orWhereNotNull($compare)
	{
		return $this->whereNotNull($compare, 'OR');
	}

	/**
	 * Adds a "where between" to sentence
	 *
	 * @access	public
	 * @param	string			$compare	Column or value to compare
	 * @param	string			$first		First value of range
	 * @param	string			$second		Second value of range
	 * @param	string			$type		Where union type (and, or)
	 * @return	mixed
	 */
	public function whereBetween($compare, $first, $second, $type = 'AND')
	{
		return $this->rawWhere(
			"{$compare} BETWEEN {$first} AND {$second}",
			$type
		);
	}

	/**
	 * Adds a "where between" to sentence of type "Or"
	 *
	 * @access	public
	 * @param	string			$compare	Campo o valor a comparar
	 * @param	string			$first		Primer valor del rango
	 * @param	string			$second		Segundo valor del rango
	 * @return	mixed
	 */
	public function orWhereBetween($compare, $first, $second)
	{
		return $this->whereBetween($compare, $first, $second, 'OR');
	}

	/**
	 * Adds a join between tables
	 *
	 * @access	public
	 * @param	string				$join			Foreign table
	 * @param	string|Clousure		$joinColumn		Foreign table key|Closure for nested joins
	 * @param	string				$operator		Operator
	 * @param	string				$tableColumn	This table key
	 * @param	string				$type			Type of join
	 * @return	mixed
	 */
	public function join(
		$join,
		$joinColumn,
		$operator = null,
		$tableColumn = null,
		$type = 'INNER'
	) {
		if ($joinColumn instanceof Closure) {
			$sentence = $this->newSelf();
			$joinColumn($sentence);
			$on = \join(' ', $sentence->on);
			$this->join[] = "{$type} JOIN {$join} ON {$on}";
			return $this;
		}
		$this->join[] = "{$type} JOIN {$join} ON {$joinColumn} {$operator} {$tableColumn}";
		return $this;
	}

	/**
	 * Adds a join between tables of type "Inner"
	 *
	 * @access	public
	 * @param	string				$join			Foreign table
	 * @param	string|Clousure		$joinColumn		Foreign table key|Closure for nested joins
	 * @param	string				$operator		Operator
	 * @param	string				$tableColumn	This table key
	 * @return	mixed
	 */
	public function innerJoin(
		$join,
		$joinColumn,
		$operator = null,
		$tableColumn = null
	) {
		return $this->join($join, $joinColumn, $operator, $tableColumn);
	}

	/**
	 * Adds a join between tables of type "Left"
	 *
	 * @access	public
	 * @param	string				$join			Foreign table
	 * @param	string|Clousure		$joinColumn		Foreign table key|Closure for nested joins
	 * @param	string				$operator		Operator
	 * @param	string				$tableColumn	This table key
	 * @return	mixed
	 */
	public function leftJoin(
		$join,
		$joinColumn,
		$operator = null,
		$tableColumn = null
	) {
		return $this->join($join, $joinColumn, $operator, $tableColumn, 'LEFT');
	}

	/**
	 * Adds a join between tables of type "Right"
	 *
	 * @access	public
	 * @param	string				$join			Foreign table
	 * @param	string|Clousure		$joinColumn		Foreign table key|Closure for nested joins
	 * @param	string				$operator		Operator
	 * @param	string				$tableColumn	This table key
	 * @return	mixed
	 */
	public function rightJoin(
		$join,
		$joinColumn,
		$operator = null,
		$tableColumn = null
	) {
		return $this->join(
			$join,
			$joinColumn,
			$operator,
			$tableColumn,
			'RIGHT'
		);
	}

	/**
	 * Adds a join between tables of type "Full Outer"
	 *
	 * @access	public
	 * @param	string				$join			Foreign table
	 * @param	string|Clousure		$joinColumn		Foreign table key|Closure for nested joins
	 * @param	string				$operator		Operator
	 * @param	string				$tableColumn	This table key
	 * @return	mixed
	 */
	public function fullOuterJoin(
		$join,
		$joinColumn,
		$operator = null,
		$tableColumn = null
	) {
		return $this->join(
			$join,
			$joinColumn,
			$operator,
			$tableColumn,
			'FULL OUTER'
		);
	}

	/**
	 * Adds a join condition
	 *
	 * @access	public
	 * @param	string		$joinColumn		Foreign table key
	 * @param	string		$operator		Operator
	 * @param	string		$tableColumn	This table key
	 * @param	string		$type			On union type (and, or)
	 * @return	mixed
	 */
	public function on($joinColumn, $operator, $tableColumn, $type = 'AND')
	{
		if (\is_string($tableColumn) && \strpos($tableColumn, '.') === false) {
			$tableColumn = new Quote($tableColumn);
		}
		$this->on[] = empty($this->on)
			? "{$joinColumn} {$operator} {$tableColumn}"
			: "{$type} {$joinColumn} {$operator} {$tableColumn}";
		return $this;
	}

	/**
	 * Adds a join condition of type "Or"
	 *
	 * @access	public
	 * @param	string		$joinColumn		Foreign table key
	 * @param	string		$operator		Operator
	 * @param	string		$tableColumn	This table key
	 * @return	mixed
	 */
	public function orOn($joinColumn, $operator, $tableColumn)
	{
		return $this->on($joinColumn, $operator, $tableColumn, 'OR');
	}

	/**
	 * Adds a join "on in" condition
	 *
	 * @access	public
	 * @param	string		$joinColumn		Foreign table key
	 * @param	array|Quote	$values			Values
	 * @param	string		$type			On union type (and, or)
	 * @param	string		$operator		Operator
	 * @return	mixed
	 */
	public function onIn($joinColumn, $values, $type = 'AND', $operator = 'IN')
	{
		if (empty($values)) {
			return $this;
		}
		$data = $values instanceof Quote ? \join(',', $values->value) : $values;
		return $this->on($joinColumn, $operator, "({$data})", $type);
	}

	/**
	 * Adds a join "on not in" condition
	 *
	 * @access	public
	 * @param	string		$joinColumn		Foreign table key
	 * @param	array		$values			Values
	 * @param	string		$type			On union type (and, or)
	 * @return	mixed
	 */
	public function onNotIn($joinColumn, array $values, $type = 'AND')
	{
		return $this->onIn($joinColumn, $values, $type, 'NOT IN');
	}

	/**
	 * Adds a raw having
	 *
	 * @access	private
	 * @param	string		$having		Having operation
	 * @param	string		$type		Having union type (and, or)
	 * @return	mixed
	 */
	private function rawHaving($having, $type = 'AND')
	{
		$having = empty($this->having) ? $having : "{$type} {$having}";
		$this->having[] = $having;
		return $this;
	}

	/**
	 * Adds a having with "And" type
	 *
	 * Add a closure as first param for nested having
	 *
	 * @access	public
	 * @param	string/Closure	$compare	Column of value to compare|Closure for nested having
	 * @param	mixed			$operator	Operator
	 * @param	mixed			$to			Value of comparison
	 * @param	string			$type		Having union type (And, Or)
	 * @return	mixed
	 */
	public function having(
		$compare,
		$operator = null,
		$to = null,
		$type = 'AND'
	) {
		if ($compare instanceof Closure) {
			$sentence = $this->newSelf();
			$compare($sentence);
			$having = \join(' ', $sentence->having);
			return $this->rawHaving("({$having})", $type);
		}
		if (!isset($to)) {
			$to = $operator;
			$operator = '=';
		}
		if (\is_string($to)) {
			$to = new Quote($to);
		}
		return $this->rawHaving("{$compare} {$operator} {$to}");
	}

	/**
	 * Adds a having with "And" type
	 *
	 * Add a closure as first param for nested having
	 *
	 * @access	public
	 * @param	string/Closure	$compare	Column of value to compare|Closure for nested having
	 * @param	mixed			$operator	Operator
	 * @param	mixed			$to			Value of comparison
	 * @return	mixed
	 */
	public function andHaving($compare, $operator = null, $to = null)
	{
		return $this->having($compare, $operator, $to);
	}

	/**
	 * Adds a having with "Or" type
	 *
	 * Add a closure as first param for nested having
	 *
	 * @access	public
	 * @param	string/Closure	$compare	Column of value to compare|Closure for nested having
	 * @param	mixed			$operator	Operator
	 * @param	mixed			$to			Value of comparison
	 * @return	mixed
	 */
	public function orHaving($compare, $operator = null, $to = null)
	{
		return $this->having($compare, $operator, $to, 'OR');
	}

	/**
	 * Sets "group by" conditions
	 *
	 * @access	public
	 * @param	array		$group		Group by conditions
	 * @return	mixed
	 */
	public function groupBy(array $group)
	{
		$this->groupBy = $group;
		return $this;
	}

	/**
	 * Sets "order by" conditions
	 *
	 * @access	public
	 * @param	array		$order		Order by conditions
	 * @return	mixed
	 */
	public function orderBy(array $order)
	{
		$this->orderBy = $order;
		return $this;
	}

	/**
	 * Sets query "limit"
	 *
	 * @access	public
	 * @param	string		$limit		Query limit
	 * @return	mixed
	 */
	public function limit($limit)
	{
		$this->limit = $limit;
		return $this;
	}

	/**
	 * Sets query "index"
	 *
	 * @access	public
	 * @param	string		$index		Query index
	 * @return	mixed
	 */
	public function index($index)
	{
		$this->index = $index;
		return $this;
	}

	/**
	 * Builds a "select" sentence
	 *
	 * @access	public
	 * @return	string
	 */
	public function buildSelect()
	{
		$columns = \join(',', $this->select);
		$query = "SELECT {$columns} FROM {$this->table}";
		if ($this->index !== '') {
			$query .= " USE INDEX ({$this->index})";
		}
		if (!empty($this->join)) {
			$query .= ' ' . \join(' ', $this->join);
		}
		if (!empty($this->where)) {
			$where = \join(' ', $this->where);
			$query .= " WHERE {$where}";
		}
		if (!empty($this->groupBy)) {
			$query .= ' GROUP BY ' . \join(', ', $this->groupBy);
		}
		if (!empty($this->having)) {
			$having = \join(' ', $this->having);
			$query .= " HAVING {$having}";
		}
		if (!empty($this->orderBy)) {
			$query .= ' ORDER BY ' . \join(', ', $this->orderBy);
		}
		if ($this->limit !== '') {
			$query .= " LIMIT {$this->limit}";
		}
		return $query;
	}

	/**
	 * Builds an "insert" sentence
	 *
	 * @access	public
	 * @param	array		$insert		Table columns and values
	 * @return	string
	 */
	public function buildInsert(array $insert)
	{
		$fields = \join(', ', $insert);
		$value = \join(', ', \array_fill(0, \count($insert), '?'));
		return "INSERT INTO {$this->table} ({$fields}) VALUES ({$value})";
	}

	/**
	 * Buils an "update" sentence
	 *
	 * @access	public
	 * @param	array		$update		Table columns and values
	 * @return	string
	 */
	public function buildUpdate(array $update)
	{
		foreach ($update as &$field) {
			$field = "{$this->table}.{$field}=?";
		}
		$fields = \join(',', $update);
		$joins = empty($this->join) ? '' : ' ' . \join(' ', $this->join);
		$where = \join(' ', $this->where);
		return "UPDATE {$this->table}{$joins} SET {$fields} WHERE {$where}";
	}

	/**
	 * Builds a "delete" sentence
	 *
	 * @access	public
	 * @param	string		$table		When join sentence, set the table to delete from
	 * @return	string
	 */
	public function buildDelete($table = ' ')
	{
		$joins = empty($this->join) ? '' : ' ' . \join(' ', $this->join);
		$where = \join(' ', $this->where);
		return "DELETE{$table}FROM {$this->table}{$joins} WHERE {$where}";
	}

	/**
	 * Returns a new instance with same table and DBInterface
	 *
	 * @access	protected
	 * @return	SqlSentence
	 */
	protected function newSelf()
	{
		return new self();
	}
}
