<?php

namespace bricksasp\promotion\models;

use Yii;

/**
 * This is the model class for table "{{%promotion_coupon}}".
 * 
 *     condition_type:[
 *       { value: 1, text: '全部商品' },
 *       { value: 2, text: '商品分类' },
 *       { value: 3, text: '指定商品' },
 *       { value: 4, text: '订单满减' },
 *     ],
 *     result_type:[
 *       { value: 1, text: '商品减固定金额' },
 *       { value: 2, text: '商品折扣' },
 *       { value: 3, text: '商品一口价' },
 *       { value: 4, text: '订单减固定金额' },
 *       { value: 5, text: '订单折扣' },
 *       { value: 6, text: '订单一口价' },
 *
 */
class PromotionCoupon extends \bricksasp\base\BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%promotion_coupon}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
            ],
            [
                'class' => \bricksasp\helpers\behaviors\UidBehavior::className(),
                'createdAtAttribute' => 'user_id',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['owner_id', 'promotion_id', 'user_id', 'status', 'type', 'content', 'start_at', 'end_at', 'created_at', 'updated_at'], 'integer'],
            [['code'], 'string', 'max' => 8],
            [['status'], 'default', 'value' => 1],
            [['code'], 'default', 'value' => Yii::$app->security->generateRandomString(6)]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'owner_id' => 'Owner ID',
            'promotion_id' => 'Promotion ID',
            'user_id' => 'User ID',
            'code' => 'Code',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getPromotion()
    {
        return $this->hasOne(Promotion::className(), ['id' => 'promotion_id'])->asArray();
    }
}
