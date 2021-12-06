<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "shows_related".
 *
 * @property int $id
 * @property int $id_show
 * @property int $related_id_show
 *
 * @property Shows $relatedIdShow
 * @property Shows $show
 */
class ShowsRelated extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shows_related';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_show', 'related_id_show'], 'required'],
            [['id_show', 'related_id_show'], 'integer'],
            [['id_show'], 'exist', 'skipOnError' => true, 'targetClass' => Shows::className(), 'targetAttribute' => ['id_show' => 'id_show']],
            [['related_id_show'], 'exist', 'skipOnError' => true, 'targetClass' => Shows::className(), 'targetAttribute' => ['related_id_show' => 'id_show']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_show' => 'Id Show',
            'related_id_show' => 'Related Id Show',
        ];
    }

    /**
     * Gets query for [[RelatedIdShow]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRelatedIdShow()
    {
        return $this->hasOne(Shows::className(), ['id_show' => 'related_id_show']);
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
