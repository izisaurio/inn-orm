<?php

namespace Inn\Data;

use Inn\Validator\DataObject, Inn\Exceptions\OrmException;

/**
 * Data model, this class allows dynamic properties
 *
 * @author	izisuario
 * @version	1
 */
#[\AllowDynamicProperties]
class Model
{
	/**
	 * Error collection when validation fails
	 * 
	 * @access	public
	 * @var		array
	 */
	public $_errors = [];

	/**
	 * Returns an array with this model properties
	 *
	 * @access	public
	 * @return	array
	 */
	public function toArray()
	{
		$properties = \get_object_vars($this);
		unset($properties['_errors']);
		return $properties;
	}

	/**
	 * Validates a model with the rules given
	 *
	 * @access	public
	 * @param	array			$rules		Model rules
	 * @param	array			$messages	Error messages array
	 * @param	string			$language	Optional label language
	 * @throws	OrmException
	 * @return	Model
	 */
	public function validate(
		array $rules,
		array $messages = null,
		$language = null
	) {
		$dataObject = new DataObject(
			$this->toArray(),
			$rules,
			$messages,
			$language
		);
		if (!$dataObject->validate()) {
			$this->_errors = $dataObject->getErrors();
			throw new OrmException(join("\n", $this->_errors));
		}
		return $this;
	}

	/**
	 * Validates a model with the rules given but only the properties with data
	 *
	 * @access	public
	 * @param	array			$rules		Model rules
	 * @param	string			$messages	Error messages file path
	 * @throws	OrmException
	 * @return	Model
	 */
	public function validateSetted(array $rules, $messages = null)
	{
		$dataObject = new DataObject(
			\array_intersect_key($rules, $this->toArray()),
			$rules,
			$messages
		);
		if (!$dataObject->validate()) {
			throw new OrmException(join("\n", $dataObject->getErrors()));
		}
		return $this;
	}

	/**
	 * Fill the model with default values given in the rules
	 *
	 * @access	public
	 * @param	array	$rules		Rules array
	 * @return	Model
	 */
	public function setDefaults(array $rules)
	{
		foreach ($rules as $key => $value) {
			if (
				!isset($this->{$key}) &&
				\is_array($value) &&
				\array_key_exists('default', $value)
			) {
				$this->{$key} = (
					new DefaultValue(
						$this,
						$value['default']
					)
				)->value;
			}
		}
		return $this;
	}

	/**
	 * Utility method to search a value in a multi level object or array
	 * 
	 * @access	public
	 * @param	array	$array		Values to travese
	 * @param	mixed	$default	Value to return if null
	 */
	public function traverse(array $array, $default = null)
	{
		$key = $this;
		foreach ($array as $property) {
			if (is_array($key)) {
				if (!isset($key[$property]) || $key[$property] === null) {
					return $default;
				}
				$key = $key[$property];
			} else {
				if (!isset($key->{$property}) || $key->{$property} === null) {
					return $default;
				}
				$key = $key->{$property};
			}
		}
		return $key;
	}
}
