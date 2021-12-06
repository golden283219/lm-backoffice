<?php

namespace common\models\queue;

use Yii;

/**
 * This is the model class for table "known_for_shows".
 *
 * @property int $id
 * @property int $id_cast
 * @property int $id_show
 *
 * @property CastImdb $cast
 * @property Shows $show
 */
class KnownForShows extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'known_for_shows';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_cast', 'id_show'], 'required'],
            [['id_cast', 'id_show'], 'integer'],
            [['id_cast'], 'exist', 'skipOnError' => true, 'targetClass' => CastImdb::className(), 'targetAttribute' => ['id_cast' => 'id']],
            [['id_show'], 'exist', 'skipOnError' => true, 'targetClass' => Shows::className(), 'targetAttribute' => ['id_show' => 'id_show']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_cast' => 'Id Cast',
            'id_show' => 'Id Show',
        ];
    }

    /**
     * Gets query for [[Cast]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCast()
    {
        return $this->hasOne(CastImdb::className(), ['id' => 'id_cast']);
    }

    /**
     * Gets query for [[Show]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShow()
    {
        return $this->hasOne(Shows::className(), ['id_show' => 'id_show']);
    }
}
