<?php

namespace bricksasp\promotion\controllers;

use Yii;
use bricksasp\promotion\models\Promotion;
use bricksasp\promotion\models\PromotionCoupon;

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
        ];
    }

	/**
	 * 可直接领取使用
	 * 
	 * @return array
	 */
    public function actionIndex()
    {
        return $this->render('index');
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
     *       @OA\Schema(ref="#/components/schemas/response"),
     *     ),
     *   ),
     * )
     *
     */
    public function actionUserCoupon()
    {
        $data = PromotionCoupon::find()->where(['owner_id'=>$this->ownerId, 'user_id'=>$this->userId])->all();
        return $this->success($data);
    }
}
