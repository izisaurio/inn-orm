<?php

require '../vendor/autoload.php';

use Inn\Database\Mysql,
	Inn\Database\Database,
	JsonToArray\Json,
	Inn\Data\DBMapper,
	Inn\Sql\Cases;

class users extends DBMapper
{
	public $properties = [
		'name' => [
			'type' => 'text',
			'isSafeText' => 'true',
			'maxLength' => 155,
			'minLength' => 5,
			'label' => 'User name',
		],
		'email' => [
			'type' => 'text',
			'isEmail' => true,
			'maxLength' => 155,
			'label' => 'User email',
		],
		'phone' => [
			'type' => 'int',
			'isInt' => true,
			'default' => 1234567890,
			'label' => 'Phone number',
		],
		'attributes' => [
			'type' => 'json',
			'isJson' => true,
			'label' => 'User attributes',
		],
	];
}

$mysql = new Mysql('localhost', 'tests', 'root', '');
$db = new Database($mysql);
$mapper = new users($db);

$select = $mapper
	->select([
		'id',
		new Cases(
			[
				'izisaurios' => 'main',
				'editted' => 'secondary',
			],
			'users.name',
			'other'
		),
	])
	->limit('5')
	->orderBy(['users.id desc'])
	->find()
	->all();

foreach ($select as $user) {
	var_dump($user->id, $user->users_name);
}

//var_dump($db->queriesLog);

$mapper = new users($db);
$select = $mapper
	->select([
		'id',
		new Cases(
			[
				'attributes->>"$.age" between 10 and 25' => 'young',
				'attributes->>"$.age" between 26 and 50' => 'middle',
				'attributes->>"$.age" between 51 and 100' => 'old',
			]
		),
	])
	->find()
	->assoc();

var_dump($select);