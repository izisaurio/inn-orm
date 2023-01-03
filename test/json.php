<?php

require '../vendor/autoload.php';

use Inn\Database\Mysql, Inn\Database\Database, mappers\users, mappers\tasks;

$mysql = new Mysql('localhost', 'tests', 'root', '');
$db = new Database($mysql);
$user = (new users($db))->select(['$attributes' => 'age as edad'])->findId(1);

//var_dump($user);

$tasks = (new tasks($db))
	->select(['name', '$users.attributes' => 'age'])
	->join('users', 'id', '=', 'idUser')
	->find()
	->all();

//var_dump($tasks);

$decoded = (new users($db))
	->select(['name', 'attributes'])
	->where('id', 1)
	->find()
	->decode(['attributes'])
	->all();
var_dump($decoded);
