<?php

require '../vendor/autoload.php';

use Inn\Database\Mysql,
	Inn\Database\Database,
	JsonToArray\Json,
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
			//'type' => 'integer', //Uncomment to test PluginDataTypeNotFoundException
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

$json = new Json('assets/errors.json');
$mysql = new Mysql('localhost', 'tests', 'root', '');
$db = new Database($mysql);
$user = (new users($db))->getModel();

$user->name = 'tester';
$user->email = 'fake@email.com';
$user->attributes = ['age' => 25, 'gender' => 'male'];
$user
	->setDefaults()
	->validate(null, $json->data)
	->save()
	->setInsertId();

var_dump($user);
