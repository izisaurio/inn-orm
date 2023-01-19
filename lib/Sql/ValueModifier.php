<?php

namespace Inn\Sql;

interface ValueModifier {
	/**
	 * Builds a string for the update sentences
	 * 
	 * @access	public
	 * @param	string	$data	Data to use on building
	 * @return	string
	 */
	public function build($data);
}