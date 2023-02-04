<?php

namespace mappers;

use Inn\Data\DBMapper;

class nullables extends DBMapper
{
	public $properties = [
		'name' => [
			'type' => 'text',
			'isSafeText' => true,
			'maxLength' => 155,
			'minLength' => 5,
			'label' => 'Subject name',
		],
		'value' => [
			'type' => 'text',
			'isSafeText' => true,
			'maxLength' => 15,
			'minLength' => 3,
			'label' => 'Value',
			'isNullable' => true,
		],
	];
}
