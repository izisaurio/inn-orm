<?php

require '../vendor/autoload.php';

use Inn\Database\Mysql,
	Inn\Database\Database,
	Inn\Database\Quote,
	mappers\users;

$mysql = new Mysql('localhost', 'tests', 'root', '');
$db = new Database($mysql);
$mapper = new users($db);

$select = $mapper
	->select(['*', '@name' => 'izisaurio'])
	->join(
		'tasks',
		fn($sentence) => $sentence
			->on('idUser', '=', 'id')
			->on('name', '=', [new Quote('Develop')])
	)
	->limit('5')
	->orderBy(['users.id desc'])
	->find()
	->all();

var_dump($select);
var_dump($db->queriesLog);
