<?php

require '../vendor/autoload.php';

use Inn\Database\Mysql, Inn\Database\Database, mappers\users;

$mysql = new Mysql('localhost', 'tests', 'root', '');
$db = new Database($mysql);
$mapper = new users($db);

$select = $mapper
	->select(['*', '@name' => 'izisaurio'])
	->limit('5')
	->orderBy(['users.id desc'])
	->find()
	->all();

var_dump($select);
var_dump($db->queriesLog);