<?php

namespace mappers;

use Inn\Data\DBUnion;

class users_subjects extends DBUnion
{
	public $properties = [
		'users' => [
			'key' => 'idUser',
			'id' => 'id',
		],
		'subjects' => [
			'key' => 'idSubject',
			'id' => 'id',
		],
	];
}
