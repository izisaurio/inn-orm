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
		':users.name' => ['izisaurio' => 'main', 'tester' => 'secondary'],
	])
	->limit('10')
	->orderBy(['id desc'])
	->find()
	->all();

var_dump($select);
var_dump($db->queriesLog);
