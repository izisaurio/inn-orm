<?php

require '../vendor/autoload.php';

use Inn\Database\Mysql, Inn\Database\Database, mappers\users;

$mysql = new Mysql('localhost', 'tests', 'root', '');
$db = new Database($mysql);
$mapper = new users($db);

$select = $mapper
	->select([
		'id',
		'@number' => 3,
		'$attributes' => 'age',
		':users.name' => ['izisaurio' => 'main', 'tester' => 'secondary'],
		'=age' => [
			'attributes->>"$.age" between 10 and 25' => 'young',
			'attributes->>"$.age" between 26 and 50' => 'middle',
			'attributes->>"$.age" between 51 and 100' => 'old',
		]
	])
	->limit('10')
	->orderBy(['id desc'])
	->find()
	->all();

var_dump($select);
var_dump($db->queriesLog);
