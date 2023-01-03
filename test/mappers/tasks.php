<?php

namespace mappers;

use Inn\Data\DBMapper;

class tasks extends DBMapper
{
	public $properties = [
		'name' => [
			'type' => 'text',
			'isSafeText' => true,
			'maxLength' => 155,
			'minLength' => 5,
			'label' => 'Task name',
		],
		'idUser' => [
			'type' => 'int',
			'isInt' => true,
			'union' => 'users|id',
			'label' => 'User',
		],
	];
}