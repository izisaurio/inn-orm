<?php

require '../vendor/autoload.php';

use Inn\Database\Mysql,
	Inn\Database\Database,
	JsonToArray\Json,
	Inn\Exceptions\OrmException,
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

$json = new Json('assets/errors.json');
$mysql = new Mysql('localhost', 'tests', 'root', '');
$db = new Database($mysql);
$user = (new users($db))->getModel();

$user->setDefaults();

var_dump($user->phone);

$user->name = 'Izisaurio';
$user->email = 'myemail';
try {
	$user->validate(null, $json->data);
}
catch (OrmException $ex) {
	var_dump($ex->getMessage());
}

//var_dump($errors->list);
