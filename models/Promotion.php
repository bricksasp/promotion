<?php

namespace bricksasp\promotion\models;

use Yii;
use bricksasp\helpers\Tools;

/**
 * This is the model class for table "{{%promotion}}".
 *
 */
class Promotion extends \bricksasp\base\BaseActiveRecord
{
    const TYPE_COUPON = 1;
    const TYPE_PROM = 2;
    const TYPE_GROUP = 3;
    const TYPE_SECKILL = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%promotion}}';
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
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'start_at',
                'updatedAtAttribute' => 'end_at',
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
            [['user_id', 'num', 'scene', 'type', 'start_at', 'end_at', 'status', 'sort', 'exclusion', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['code'], 'string', 'max' => 16],
            [['receive_num', 'scene', 'type', 'sort', 'exclusion', 'status'], 'default', 'value' => 1],
            [['code'], 'default', 'value' => Yii::$app->security->generateRandomString(6)]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'name' => 'Name',
            'num' => 'Num',
            'receive_num' => 'Receive Num',
            'scene' => 'Scene',
            'type' => 'Type',
            'code' => 'Code',
            'start_at' => 'Start Time',
            'end_at' => 'End Time',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getConditions()
    {
        return $this->hasOne(PromotionConditions::className(), ['promotion_id' => 'id'])->asArray();
    }

    public function saveData($params)
    {
        $this->load($params);

        $transaction = self::getDb()->beginTransaction();
        try {
            if (!$this->save()) {
                $transaction->rollBack();
                return false;
            }
            $params['conditions']['promotion_id'] = $this->id;
            $condition = new PromotionConditions();
            $condition->load($params['conditions']);
            if (!$condition->save()) {
                $transaction->rollBack();
                Tools::exceptionBreak('条件保存失败');
            }

            $transaction->commit();
            return true;
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch(\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        return false;
    }

    public function updateData($params)
    {
        
        $this->load($params);

        $transaction = self::getDb()->beginTransaction();
        try {
            if (!$this->save()) {
                $transaction->rollBack();
                return false;
            }
            $params['conditions']['promotion_id'] = $this->id;
            $condition = new PromotionConditions();
            $condition->load($params['conditions']);
            if (!$condition->save()) {
                $transaction->rollBack();
                Tools::exceptionBreak('条件保存失败');
            }

            $transaction->commit();
            return true;
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch(\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        return false;
    }

    /**
     * 领券
     * @param  string $value 
     * @return array
     */
    public function receiveCoupon($params)
    {
        extract($params);
        $promotion = self::findOne($promotion_id);

        $userCoupon = PromotionCoupon::find()->where(['promotion_id' => $promotion_id, 'user_id' => $user_id])->all();
        if (count($userCoupon) >= $promotion->receive_num) {
            Tools::exceptionBreak(990001);
        }

        $c = PromotionConditions::find()->where(['promotion_id' => $promotion_id])->one();
        $params['type'] = $c->type;
        $params['content'] = $c->content;
        $params['start_at'] = $promotion->start_at;
        $params['end_at'] = $promotion->end_at;
        $model = new PromotionCoupon();
        $model->load($params);
        return $model->save();
    }

}
