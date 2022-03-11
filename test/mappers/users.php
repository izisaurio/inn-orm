<?php

namespace mappers;

use Inn\Data\DBMapper;

class users extends DBMapper
{
	public $properties = [
		'name' => [
			'type' => 'text',
			'isSafeText' => true,
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