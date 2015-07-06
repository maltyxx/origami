<?php

namespace Origami;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class Database
{
	public static function connect($name)
	{
		$CI =& get_instance();
		
		if (!isset($CI->{"db_$name"})) {
			$CI->{"db_$name"} = $CI->load->database($name, TRUE);
			$CI->{"db_$name"}->initialize();
			$CI->{"db_$name"}->query("SET @@session.block_encryption_mode = 'aes-256-cbc';");
		}
		
		return $CI->{"db_$name"};
	}
	
	public static function link($name)
	{
		return self::connect($name);
	}
	
}

/* End of file Database.php */
/* Location: ./libraries/Origami/Entity/Db/Database.php */
