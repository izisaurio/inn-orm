<?php

require '../vendor/autoload.php';

use Inn\Database\Mysql,
	Inn\Database\Database,
	Inn\Database\Quote,
	mappers\users,
	mappers\subjects;

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
	->decode(['attributes'])
	->all();

var_dump($select);
var_dump($db->queriesLog);

$subjects = new subjects($db);

$result = $subjects
	->select(['id', 'name'])
	->where('id', '=', (new users($db))->select(['max(id)']))
	->find()
	->source();

var_dump($result);

$between = (new subjects($db))
	->select(['id', 'name'])
	->whereBetween('id', 2, 3)
	->find()
	->source();

var_dump($between);

var_dump($db->queriesLog);