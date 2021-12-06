<?php

use yii\db\Migration;

/**
 * Class m200918_152418_add_shows_moderation_migration
 */
class m200918_152418_add_shows_moderation_migration extends Migration
{
    /**
     * Change Database for migration
     */
    public function init()
    {
        $this->db = 'db_queue';
        parent::init();
    }

    public function safeUp()
    {
        $tableSchema = Yii::$app->db_queue->schema->getTableSchema('episodes_moderation_history');

        if ($tableSchema !== null) {
            return true;
        }

        $this->createTable('episodes_moderation_history', [
            'id' => $this->primaryKey(),
            'id_meta'           => $this->integer(9),
            'id_site_episode'   => $this->integer(),
            'title'             => $this->string(255),
            'imdb_id'           => $this->string(20),
            'tvmaze_id'         => $this->string(20),
            'air_date'          => $this->date(),
            'episode'           => $this->integer(),
            'season'            => $this->integer(),
            'priority'          => $this->tinyInteger(),
            'original_language' => $this->string(3),
            'id_user'           => $this->integer(9),
            'status'            => $this->tinyInteger(),
            'data'              => $this->json(),
            'guid'              => $this->string(100),
            'type'              => $this->tinyInteger(),
            'worker_ip'         => $this->string(50),
            'created_at'        => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at'        => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'is_deleted'        => $this->tinyInteger(1)->defaultValue(0)
        ]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('episodes_moderation_history');

        return true;
    }
}
