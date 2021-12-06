<?php

namespace console\models\queue;

class Movies extends \common\models\queue\Movies
{

	public static function FullResetMovies(array $statusCodes, $resetCode) {
		$db = self::getDb();

		$where = 'WHERE is_downloaded = ' . implode(' OR is_downloaded = ', $statusCodes);

		$count = $db->createCommand("
			UPDATE movies SET is_downloaded = '$resetCode' 
			$where
		")->execute();

		return true;
	}

	public static function PartialResetLates ($year, $resetCode) {
		$db = self::getDb();

		$db->createCommand("
			UPDATE movies SET is_downloaded = '$resetCode' 
			WHERE year = $year
		")->execute();

		return true;
	}

	public static function PartialResetLQ ($resetCode) {
		$db = self::getDb();

		$db->createCommand("
			UPDATE movies SET is_downloaded = '$resetCode'
			WHERE flag_quality < 7
			AND flag_quality <> 0
			AND is_downloaded <> 2
			AND is_downloaded <> 3
		")->execute();

		return true;
	}
}
