<?php

require '../vendor/autoload.php';

use Inn\Database\Mysql,
	Inn\Database\Database,
	mappers\users,
	mappers\users_subjects,
	mappers\subjects;

$mysql = new Mysql('localhost', 'tests', 'root', '');
$db = new Database($mysql);
$mapper = new users($db);

$user = (new users($db))->findId(1);
$user->updateUnion(new users_subjects($db), [1, 2]);

$subjects = (new subjects($db))
	->join('users_subjects', 'idSubject', '=', 'id')
	->where('users_subjects.idUser', 1)
	->find()
	->all();

var_dump($subjects);
