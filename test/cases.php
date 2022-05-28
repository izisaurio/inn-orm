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
	];
}

$mysql = new Mysql('localhost', 'tests', 'root', '');
$db = new Database($mysql);
$mapper = new users($db);

$select = $mapper
	->select([
		'id',
		new Cases(
			'users.name',
			[
				'izisaurios' => 'main',
				'editted' => 'secondary',
			],
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
