<?php

require '../vendor/autoload.php';

use Inn\Database\Mysql, Inn\Database\Database, Inn\Data\DBMapper;

class users extends DBMapper
{
	public $properties = [
		'name' => [
			'type' => 'text',
			'max_length' => 155,
			'min_length' => 5,
			'label' => 'User name',
		],
		'email' => [
			'type' => 'text',
			'email' => true,
			'max_length' => 155,
			'label' => 'User email',
		],
		'phone' => [
			'type' => 'int',
			'default' => 1234567890,
			'label' => 'Phone number',
		],
	];
}

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
$mapper = new users($db);

$select = $mapper
	->select(['*', (new tasks($db))->select(['name'])->where('id', 1)])
	->limit('5')
	->orderBy(['id desc'])
	->find()
	->all();

var_dump($select);
var_dump($db->queriesLog);