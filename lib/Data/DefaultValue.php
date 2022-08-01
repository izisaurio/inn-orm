<?php

namespace Inn\Data;

use \DateTime;

class DefaultValue
{
	/**
	 * Value of default property
	 *
	 * @access	public
	 * @var		mixed
	 */
	public $value;

	/**
	 * Construct
	 *
	 * Sets de final default value
	 *
	 * @access	public
	 * @param	Model	$model			Model to be setted
	 * @param	mixed	$defaultValue	Default value received
	 */
	public function __construct(Model $model, $defaultValue)
	{
		if ($defaultValue === '!now') {
			$this->value = (new DateTime())->format('Y-m-d H:i:s');
		} elseif ($defaultValue === '!today') {
			$this->value = (new DateTime())->format('Y-m-d');
		} elseif (\is_string($defaultValue) && $defaultValue[0] === '@') {
			$key = \ltrim($defaultValue, '@');
			$this->value = isset($model->{$key}) ? $model->{$key} : '';
		} else {
			$this->value = $defaultValue;
		}
	}
}
