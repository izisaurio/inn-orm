<?php

require '../vendor/autoload.php';

use Inn\Database\Mysql,
	Inn\Database\Database,
	Inn\Sql\JsonSet,
	Inn\Data\DBMapper;

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
$user = (new users($db))->select(['*'])->findId(1, ['attributes']);

$user->name = 'SlayTheSpire';
$user->attributes = ['age' => 36];
$user
	->validate()
	->save()
	->setInsertId();

$user->attributes = new JsonSet('name', 'izi');
$user->save();

//var_dump($user);

(new users($db))
	->whereNotNull('attributes')
	->updateAll(['attributes' => new JsonSet('name', 'izisaurio')]);
