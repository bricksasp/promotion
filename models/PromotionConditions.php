<?php

namespace bricksasp\promotion\models;

use Yii;

/**
 * This is the model class for table "{{%promotion_conditions}}".
 *
 */
class PromotionConditions extends \bricksasp\base\BaseActiveRecord
{
    //1全部2分类3部分4订单满减
    const TYPE_ALL = 1;
    const TYPE_CAT = 2;
    const TYPE_PART = 3;
    const TYPE_REDUCTION = 4;

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
            [['promotion_id', 'condition_type', 'condition', 'result_type'], 'integer'],
            [['content', 'result'], 'string', 'max' => 255],
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
    
    public function getPromotion()
    {
        return $this->hasOne(Promotion::className(), ['id' => 'promotion_id'])->asArray();
    }
}
