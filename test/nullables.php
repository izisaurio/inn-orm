<?php

require '../vendor/autoload.php';

use Inn\Database\Mysql,
	Inn\Database\Database,
	JsonToArray\Json,
	mappers\nullables;

$json = new Json('assets/errors.json');
$mysql = new Mysql('localhost', 'tests', 'root', '');
$db = new Database($mysql);
$mapper = new nullables($db);
$nullable = $mapper->getModel();

$nullable->name = 'Soy null';
$nullable->value = null;
$nullable
	->setDefaults()
	->validate(null, $json->data)
	->save()
	->setInsertId();

var_dump($nullable);
