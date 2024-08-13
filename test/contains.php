<?php

require '../vendor/autoload.php';

use Inn\Database\Mysql, Inn\Database\Database, mappers\users;

$mysql = new Mysql('localhost', 'tests', 'root', '');
$db = new Database($mysql);

$users = (new users($db))
	->select(['*'])
    ->where(fn($sentence) =>
        $sentence
            ->whereJsonContains('attributes->>"$.hobbies"', 4)
            ->orWhereJsonContains('attributes->>"$.hobbies"', '"sports"')
    )
	->find()
    ->decode(['attributes'])
	->all();

var_dump($users, $db->queriesLog);

$noUsers = (new users($db))
	->select(['*'])
	->whereJsonContains('attributes->>"$.hobbies"', '"economics"')
	->find()
    ->decode(['attributes'])
	->all();

var_dump($noUsers);