<?php

namespace console\models\queue;

use yii\helpers\ArrayHelper;

class Shows extends \common\models\queue\Shows
{

	public function getShowsMeta()
	{
		return $this->hasMany(ShowsMeta::className(), ['id_tvshow' => 'id_tvshow'])->orderBy([
			'season' => SORT_DESC,
			'episode' => SORT_DESC,
		]);
	}

	public function getYear()
    {
        preg_match('/(\d{4})-([0][1-9]|10|11|12)-(([0,1,2][0-9])|3[0,1])/m', $this->first_air_date, $matches);

        if (!empty($this->first_air_date) && ArrayHelper::getValue($matches, '0') === $this->first_air_date) {
            return ArrayHelper::getValue($matches, '1');
        }

        return null;
    }

}
