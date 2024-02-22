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

$withId = (new users($db))->select(['name', 'attributes'])->findId(1, ['attributes']);

//var_dump($withId);

$decoded = (new users($db))
	->select(['name', 'attributes'])
	->where('id', 1)
	->find()
	->decode(['attributes'])
	->all();

var_dump($decoded);

//Traverse json data
$jobName = $decoded[0]->traverse(['attributes', 'data', 'job', 'name']);

var_dump("Traversed: {$jobName}");

$traversedDefault = $decoded[0]->traverse(['attributes', 'data', 'job', 'position'], 'Default');

var_dump("Traversed default: {$traversedDefault}");

$traversedNull = $decoded[0]->traverse(['attributes', 'data', 'job', 'position']);

var_dump($traversedNull, isset($traversedNull));

//When decoding a non json value it sets it as null
$withError = (new users($db))
	->select(['name', 'attributes'])
	->where('id', 1)
	->find()
	->decode(['attributes', 'name'])
	->all();

var_dump($withError);