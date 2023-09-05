<?php

namespace Inn\Sql;

/**
 * Value type for updating json attributes
 *
 * @author	izisaurio
 * @version	1
 */
class JsonSet implements ValueModifier
{
	/**
	 * Attribute
	 *
	 * @access	public
	 * @var		string
	 */
	public $attribute;

	/**
	 * Value after update
	 *
	 * @access	public
	 * @var		mixed
	 */
	public $value;

	/**
	 * Constructor
	 *
	 * Builds the sql
	 *
	 * @access	public
	 * @param	string	$attribute	Json attribute
	 * @param	mixed	$value		Atribute's new value
	 */
	public function __construct($attribute, $value)
	{
		$this->attribute = $attribute;
		$this->value = $value;
	}

	/**
	 * Builds the json_set
	 *
	 * @access	public
	 * @param	string	$data		Column to update
	 * @return	string
	 */
	public function build($data)
	{
		return "JSON_SET({$data}, '$.{$this->attribute}', '{$this->value}')";
	}
}
