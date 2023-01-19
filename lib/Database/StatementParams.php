<?php

namespace Inn\Database;

use \StdClass;

/**
 * Statement param collection
 *
 * @author	izisaurio
 * @version	1
 */
class StatementParams
{
	/**
	 * Param array
	 *
	 * @access	public
	 * @var		array
	 */
	public $items = [];

	/**
	 * Adds a param to collection
	 *
	 * @access	public
	 * @param	string	$attr		Param type
	 * @param	mixed	$value		Param value
	 */
	public function add($attr, $value)
	{
		$param = new StdClass();
		$param->attr = $attr;
		$param->value =
			\is_array($value) || $value instanceof StdClass
				? \json_encode($value)
				: $value;
		$this->items[] = $param;
	}
}
