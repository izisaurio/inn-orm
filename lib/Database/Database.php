<?php

namespace Inn\Database;

use \PDO,
	\PDOException,
	Inn\Exceptions\DatabaseConnectException,
	Inn\Exceptions\DatabaseStatementException;

/**
 * PDO connection wrapper
 *
 * @author	izisaurio
 * @version	1
 */
class Database
{
	/**
	 * Database type plugin
	 *
	 * @access	public
	 * @var		Database\Plugin
	 */
	public $plugin;

	/**
	 * Connection
	 *
	 * @access	public
	 * @var		PDO
	 */
	public $connection;

	/**
	 * Statement
	 *
	 * @access	protected
	 * @var		\PDOStatement
	 */
	protected $statement;

	/**
	 * Log of queries for current connection
	 *
	 * @access	public
	 * @var		array
	 */
	public $queriesLog = [];

	/**
	 * Constructor
	 *
	 * Creates the connection
	 *
	 * @access	public
	 * @param	Plugin	$plugin		Database connection plugin
	 * @throws	Exceptions\DatabaseConnectException
	 */
	public function __construct(Plugin $plugin)
	{
		$this->plugin = $plugin;
		try {
			$this->connection = new PDO(
				$this->plugin->dsn,
				$this->plugin->user,
				$this->plugin->password,
				[
					PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->plugin->charset}",
				]
			);
		} catch (PDOException $ex) {
			throw new DatabaseConnectException($ex->getCode());
		}
		Quote::setDatabase($this);
	}

	/**
	 * Processes a statement
	 *
	 * @access	public
	 * @param	string			$query		Query sentence
	 * @param	StatementParams	$params		Params added to sentence
	 * @throws	Exceptions\DatabaseStatementException
	 */
	public function processStatement($query, StatementParams $params = null)
	{
		$this->queriesLog[] = $query;
		if (isset($params)) {
			$this->prepareStatement($query);
			$this->bindParams($params);
			$this->executeStatement();
			return;
		}
		$this->statement = $this->connection->query($query);
		if ($this->statement === false) {
			throw new DatabaseStatementException(
				join(';', $this->connection->errorInfo()),
				$query
			);
		}
	}

	/**
	 * Creates a prepared statement
	 *
	 * @access	public
	 * @param	string	$query		Query sentence
	 * @throws	Exceptions\DatabaseStatementException
	 */
	public function prepareStatement($query)
	{
		$this->statement = $this->connection->prepare($query);
		if ($this->statement === false) {
			throw new DatabaseStatementException(
				'Statement prepare error',
				$query
			);
		}
	}

	/**
	 * Add params to prepared sentence
	 *
	 * @access	public
	 * @param	StatementParams	$params		Params to add
	 * @throws	Exceptions\DatabaseStatementException
	 */
	public function bindParams(StatementParams $params)
	{
		foreach ($params->items as $key => $param) {
			if (
				!$this->statement->bindValue(
					$key + 1,
					$param->value,
					$this->plugin->translate($param->attr)
				)
			) {
				throw new DatabaseStatementException(
					\join(';', $this->statement->errorInfo()),
					\end($this->queriesLog)
				);
			}
		}
	}

	/**
	 * Executes a prepared statement
	 *
	 * @access	public
	 * @throws	Exceptions\DatabaseStatementException
	 */
	public function executeStatement()
	{
		if (!$this->statement->execute()) {
			throw new DatabaseStatementException(
				\join(';', $this->statement->errorInfo()),
				\end($this->queriesLog)
			);
		}
	}

	/**
	 * Search query
	 *
	 * @access	public
	 * @param	string			$query		Search query sentence
	 * @param	StatementParams	$params		Params added to sentence
	 * @return	array
	 * @throws	Exceptions\DatabaseStatementException
	 */
	public function search($query, StatementParams $params = null)
	{
		$this->processStatement($query, $params);
		$results = $this->statement->fetchAll(PDO::FETCH_ASSOC);
		if ($results === false) {
			throw new DatabaseStatementException(
				\join(';', $this->statement->errorInfo()),
				$query
			);
		}
		return $results;
	}

	/**
	 * Non search query execution
	 *
	 * @access	public
	 * @param	string			$query		Non search query sentence
	 * @param	StatementParams	$params		Params added to sentence
	 * @throws	Exceptions\DatabaseStatementException
	 */
	public function execute($query, StatementParams $params = null)
	{
		$this->processStatement($query, $params);
	}
}
