<?php

require '../vendor/autoload.php';

use Inn\Database\Mysql,
	Inn\Database\Database,
	Inn\Database\Quote,
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
	];
}

$mysql = new Mysql('localhost', 'tests', 'root', '');
$db = new Database($mysql);
$user = (new users($db))->select(['*'])->findId(new Quote(2));

$user->name = 'SlayTheSpire';
$user->validate(null, 'assets/errors.json')->save();

var_dump($user);
