<?php

namespace Origami;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Origami ORM (objet relationnel mapping)
 * @author Yoann VANITOU
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link https://github.com/maltyxx/origami
 */
class DB
{
	public static function link($name)
	{
		$CI =& get_instance();

		if (!isset($CI->{"db_$name"})) {
			$CI->{"db_$name"} = $CI->load->database($name, TRUE);
			$CI->{"db_$name"}->initialize();

            if ($CI->origami->getConfig('encryption_enable')) {
                $CI->{"db_$name"}->query("SET @@session.block_encryption_mode = 'aes-256-cbc';");
            }
		}

		return $CI->{"db_$name"};
	}

	public static function get($name)
	{
		return self::link($name);
	}

}

/* End of file Database.php */
/* Location: ./libraries/Origami/Database.php */
