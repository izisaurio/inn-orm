<?php

require '../vendor/autoload.php';

use Inn\Database\Mysql, Inn\Database\Database, mappers\users;

$mysql = new Mysql('localhost', 'tests', 'root', '');
$db = new Database($mysql);
$mapper = new users($db);

$indexed = $mapper
	->select(['*'])
	->limit('5')
	->orderBy(['users.id desc'])
	->find()
	->indexed('id');

var_dump($indexed);

$indexed = $mapper
	->select(['*'])
	->limit('5')
	->orderBy(['users.id desc'])
	->find()
	->indexedSource('id');

var_dump($indexed);
