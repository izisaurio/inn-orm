<?php

namespace mappers;

use Inn\Data\DBMapper;

class subjects extends DBMapper
{
	public $properties = [
		'name' => [
			'type' => 'text',
			'isSafeText' => true,
			'maxLength' => 155,
			'minLength' => 5,
			'label' => 'Subject name',
		],
	];
}