<?php

namespace bricksasp\promotion\models;

use Yii;
use bricksasp\helpers\Tools;

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
 *       result_type 对应 优惠券 type
 */
class PromotionCoupon extends \bricksasp\base\BaseActiveRecord
{
    const STATUS_NO = 1; //未使用
    const STATUS_USED = 2; //已使用
    const EXCLUSION_NO = 2; //不排他
    const EXCLUSION_YES = 1; //排他
    // 类型：1商品减固定金额2商品折扣3商品一口价4订单减固定金额5订单折扣6订单一口价
    const RESULT_GOODS_AMOUT = 1;
    const RESULT_GOODS_DISCOUNT = 2;
    const RESULT_GOODS_PRICE = 3;
    
    const RESULT_ORDER_AMOUT = 4;
    const RESULT_ORDER_DISCOUNT = 5;
    const RESULT_ORDER_PRICE = 6;

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
            [['id', 'owner_id', 'promotion_id', 'user_id', 'status', 'type', 'content', 'start_at', 'end_at', 'exclusion', 'created_at', 'updated_at'], 'integer'],
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

    public function getConditions()
    {
        return $this->hasOne(PromotionConditions::className(), ['promotion_id' => 'promotion_id']);
    }

    public function checkEffectiveness($ids=[])
    {
        $coupons = $this->find()->with(['conditions'])->where(['id' => $ids])->all();
        if (!$coupons) {
            Tools::exceptionBreak(Yii::t('base', 40002, '优惠券'));
        }
        $cps = [];
        foreach ($coupons as $item) {
            if ($item->start_at > time()) {
                Tools::exceptionBreak(990002);
            }
            if ($item->end_at < time()) {
                Tools::exceptionBreak(990003);
            }
            if ($item->status == self::STATUS_USED) {
                Tools::exceptionBreak(990004);
            }
            if (count($ids) > 1 && $item->exclusion == self::EXCLUSION_YES) {
                Tools::exceptionBreak(990005);
            }
            $cps['conditions'][$item->conditions->condition_type][] = $item->conditions->content;
            $cps['result'][$item->type][] = $item->content;
        }
        return $cps;
    }
}
