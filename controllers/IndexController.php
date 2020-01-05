<?php

namespace bricksasp\promotion\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use bricksasp\promotion\models\Promotion;

class IndexController extends \bricksasp\base\BaseController
{
	/**
	 * 促销信息列表
	 * @return array
	 * 
     * @OA\Get(path="/promotion/index/index",
     *   summary="促销列表",
     *   tags={"promotion模块"},
     *   @OA\Parameter(
     *     description="开启平台功能后，访问商户对应的数据标识，未开启忽略此参数",
     *     name="access-token",
     *     in="query",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     description="当前叶数",
     *     name="page",
     *     in="query",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Parameter(
     *     description="每页行数",
     *     name="pageSize",
     *     in="query",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="列表数据",
     *     @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(ref="#/components/schemas/promotionList"),
     *     ),
     *   ),
     * )
     *
     * @OA\Schema(
     *   schema="promotionList",
     *   description="列表结构",
     *   allOf={
     *     @OA\Schema(
     *       @OA\Property(property="id", type="integer", description="促销id"),
     *       @OA\Property(property="name", type="string", description="促销名称"),
     *       @OA\Property( property="start_time", type="integer", description="开始时间"),
     *       @OA\Property( property="end_time", type="integer", description="结束时间" ),
     *       @OA\Property( property="status", type="string", description="1默认2可直接领取" ),
     *     )
     *   }
     * )
	 */
    public function actionIndex()
    {
        $params = Yii::$app->request->queryParams;
        $dataProvider = new ActiveDataProvider([
            'query' => Promotion::find($this->dataOwnerUid()),
            'pagination' => [
                'pageSize' => $params['pageSize'] ?? 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ],
        ]);
        return $this->pageFormat($dataProvider);
    }

}
