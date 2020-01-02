<?php

namespace bricksasp\promotion\models;

use Yii;

/**
 * This is the model class for table "{{%promotion_conditions}}".
 *
 */
class PromotionConditions extends \bricksasp\base\BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%promotion_conditions}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['promotion_id', 'type'], 'integer'],
            [['content'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'promotion_id' => 'Promotion ID',
            'content' => 'Content',
            'type' => 'Type',
        ];
    }
}
