<?php

require '../vendor/autoload.php';

use Inn\Database\Mysql,
	Inn\Database\Database,
	Inn\Data\DBMapper;

class tasks extends DBMapper
{
	public $properties = [
		'name' => [
			'type' => 'text',
			'isSafeText' => true,
			'maxLength' => 155,
			'minLength' => 5,
			'label' => 'Task name',
		],
		'idUser' => [
			'type' => 'int',
			'isInt' => true,
			'union' => 'users|id',
			'label' => 'User',
		],
	];
}

$mysql = new Mysql('localhost', 'tests', 'root', '');
$db = new Database($mysql);
$mapper = new tasks($db);

$select = $mapper
	->select(['tasks.*', 'idUser' => 'name'])
	->limit('5')
	->orderBy(['users.id desc'])
	->find()
	->all();

var_dump($select);
