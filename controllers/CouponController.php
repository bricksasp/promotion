<?php

namespace bricksasp\promotion\controllers;

use Yii;
use bricksasp\promotion\models\Promotion;
use bricksasp\promotion\models\PromotionCoupon;
use bricksasp\promotion\models\PromotionConditions;

class CouponController extends \bricksasp\base\BaseController
{
    /**
     * 登录可访问 其他需授权
     * @return array
     */
    public function allowAction()
    {
        return [
            'create',
            'update',
            'delete',
            'receive',
            'user-coupon',
        ];
    }

    /**
     * 免登录可访问
     * @return array
     */
    public function allowNoLoginAction()
    {
        return [
            'index',
            'goods',
            'code',
        ];
    }

	/**
	 * 可直接领取优惠券列表
	 * 
	 * @return array
     * 
     * @OA\Get(path="/promotion/coupon/index",
     *   summary="优惠券列表",
     *   tags={"promotion模块"},
     *   @OA\Parameter(
     *     description="登录凭证",
     *     name="X-Token",
     *     in="header",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="相应结构",
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(ref="#/components/schemas/couponList"),
     *     ),
     *   ),
     * )
     *
	 */
    public function actionIndex()
    {
        $data = Promotion::find()->with(['conditions'])->where(['user_id'=>$this->ownerId, 'type' => Promotion::TYPE_COUPON, 'status' => 2])->asArray()->all();
        $data['userCoupon'] = (object)[];
        if ($this->uid) {
            $userCoupon = PromotionCoupon::find()->select(['promotion_id'])->where(['owner_id'=>$this->ownerId, 'user_id'=>$this->uid])->asArray()->all();
            $data['userCoupon'] = array_column($userCoupon, 'promotion_id');
        }
        return $this->success($data);
    }

    /**
     * @OA\Get(path="/promotion/coupon/receive",
     *   summary="领取优惠券",
     *   tags={"promotion模块"},
     *   @OA\Parameter(
     *     description="登录凭证",
     *     name="X-Token",
     *     in="header",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     description="促销id",
     *     name="promotion_id",
     *     in="query",
     *     required=true,
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="相应结构",
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(ref="#/components/schemas/response"),
     *     ),
     *   ),
     * )
     *
     */
    public function actionReceive()
    {
        $model = new Promotion();
        return $model->receiveCoupon($this->queryFilters()) ? $this->success() : $this->fail();
    }

    /**
     * @OA\Get(path="/promotion/coupon/user-coupon",
     *   summary="用户已领取优惠券列表",
     *   tags={"promotion模块"},
     *   @OA\Parameter(
     *     description="登录凭证",
     *     name="X-Token",
     *     in="header",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="相应结构",
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(ref="#/components/schemas/couponList"),
     *     ),
     *   ),
     * )
     *
     * @OA\Schema(
     *   schema="couponList",
     *   description="列表结构",
     *   allOf={
     *     @OA\Schema(
     *       @OA\Property(property="code", type="string", description="优惠券代码"),
     *       @OA\Property(property="status", type="integer", description="使用状态'1正常2已使用"),
     *       @OA\Property( property="start_at", type="integer", description="开始时间"),
     *       @OA\Property( property="end_at", type="integer", description="结束时间" ),
     *       @OA\Property( property="type", type="integer", description="1全部2分类3部分商品4订单满减" ),
     *       @OA\Property(property="content", type="string", description="type对应值"),
     *       @OA\Property(property="conditions", type="array", description="促销规则", @OA\Items(
     *           @OA\Property(
     *               description="促销id",
     *               property="promotion_id",
     *               type="integer"
     *           ),
     *           @OA\Property(
     *               description="优惠金额",
     *               property="content",
     *               type="integer"
     *           )
     *         )
     *       ),
     *     )
     *   }
     * )
     */
    public function actionUserCoupon()
    {
        $data = PromotionCoupon::find()->where(['owner_id'=>$this->ownerId, 'user_id'=>$this->uid])->all();
        return $this->success($data);
    }

    /**
     * @OA\Get(path="/promotion/coupon/goods",
     *   summary="商品优惠券",
     *   tags={"promotion模块"},
     *   @OA\Parameter(
     *     description="商品id",
     *     name="id",
     *     in="query",
     *     required=true,
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="相应结构",
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(ref="#/components/schemas/couponGoods"),
     *     ),
     *   ),
     * )
     *
     *
     * @OA\Schema(
     *   schema="couponGoods",
     *   description="列表结构",
     *   allOf={
     *     @OA\Schema(
     *       @OA\Property(property="promotion_id", type="integer", description="促销id"),
     *       @OA\Property( property="type", type="integer", description="1全部2分类3部分商品4订单满减" ),
     *       @OA\Property(property="content", type="string", description="type对应值"),
     *       @OA\Property(property="promotion", type="array", description="促销信息", @OA\Items(
     *           @OA\Property(
     *               description="名称",
     *               property="name",
     *               type="integer"
     *           ),
     *           @OA\Property(
     *               description="开始时间",
     *               property="start_at",
     *               type="integer"
     *           ),
     *           @OA\Property(
     *               description="结束时间",
     *               property="end_at",
     *               type="integer"
     *           ),
     *         )
     *       )
     *     )
     *   }
     * )
     */
    public function actionGoods()
    {
        $goods_id = Yii::$app->request->get('id');
        $data = PromotionConditions::find()->with(['promotion'])->where(['type'=>PromotionConditions::TYPE_PART, 'content' => $goods_id])->asArray()->all();

        return $this->success($data);
    }

    /**
     * @OA\Get(path="/promotion/coupon/code",
     *   summary="代码获取促销信息",
     *   tags={"promotion模块"},
     *   @OA\Parameter(
     *     description="促销代码",
     *     name="codes",
     *     in="query",
     *     required=true,
     *     @OA\Schema(
     *       type="string",
     *       default="default_1,default_2,default_3,default_4"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="相应结构",
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(ref="#/components/schemas/couponList"),
     *     ),
     *   ),
     * )
     *
     */
    public function actionCode()
    {
        $codes = array_filter(explode(',', Yii::$app->request->get('codes')));
        $data = Promotion::find()->select(['id', 'name', 'type', 'code', 'start_at', 'end_at', 'exclusion'])->with(['conditions'])->where(['user_id'=>$this->ownerId, 'type' => Promotion::TYPE_COUPON, 'code' => $codes])->asArray()->all();
        $data['userCoupon'] = (object)[];
        if ($this->uid) {
            $userCoupon = PromotionCoupon::find()->select(['promotion_id'])->where(['owner_id'=>$this->ownerId, 'user_id'=>$this->uid])->asArray()->all();
            $data['userCoupon'] = array_column($userCoupon, 'promotion_id');
        }
        return $this->success($data);
    }

    
}
