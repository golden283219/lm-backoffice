<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "metadata_scraper_proxy_servers".
 *
 * @property int $id
 * @property string|null $proxy_url
 * @property int|null $usages
 * @property int|null $enabled
 */
class MetadataScraperProxyServers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'metadata_scraper_proxy_servers';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_queue');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usages', 'enabled'], 'integer'],
            [['proxy_url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'proxy_url' => 'Proxy Url',
            'usages' => 'Usages',
            'enabled' => 'Enabled',
        ];
    }
}
