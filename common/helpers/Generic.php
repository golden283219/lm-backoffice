<?php

namespace common\helpers;

class Generic {
	
	public static function base64_md5_hash ($data) {

		return str_replace('=', '', strtr(base64_encode(md5($data, TRUE)), '+/', '-_'));

	}

}