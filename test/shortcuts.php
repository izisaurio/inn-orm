<?php

require '../vendor/autoload.php';

use Inn\Database\Mysql, Inn\Database\Database, mappers\users;

$mysql = new Mysql('localhost', 'tests', 'root', '');
$db = new Database($mysql);
$mapper = new users($db);

$select = $mapper
	->select([
		'id',
		'@' => 3,
		':users.name' => ['izisaurio' => 'main', 'editted' => 'secondary'],
	])
	->limit('5')
	->orderBy(['users.id desc'])
	->find()
	->all();

var_dump($select);
