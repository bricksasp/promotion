<?php

namespace bricksasp\promotion\controllers;

use Yii;
use bricksasp\promotion\models\Promotion;
use yii\data\ActiveDataProvider;
use bricksasp\base\BaseController;
use yii\web\HttpException;
use yii\filters\VerbFilter;

/**
 * IndexController implements the CRUD actions for Promotion model.
 */
class PromotionController extends BaseController
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
     * 促销信息列表
     * @return array
     * 
     * @OA\Get(path="/promotion/promotion/index",
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
     *       @OA\Property( property="start_at", type="integer", description="开始时间"),
     *       @OA\Property( property="end_at", type="integer", description="结束时间" ),
     *       @OA\Property( property="status", type="string", description="1默认2可直接领取" ),
     *     )
     *   }
     * )
     */
    public function actionIndex()
    {
        $params = Yii::$app->request->queryParams;
        $dataProvider = new ActiveDataProvider([
            'query' => Promotion::find($this->dataOwnerUid())->where(['type' => $params['type'] ?? 1]),
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

    /**
     * Displays a single Promotion model.
     * @param integer $id
     * @return mixed
     * @throws HttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel(Yii::$app->request->get('id'));
        $data = $model->toArray();;
        $data['conditions'] = $model->conditions;
        return $this->success($data);
    }

    /**
     * Creates a new Promotion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Promotion();

        if ($model->saveData(Yii::$app->request->post())) {
            return $this->success();
        }

        return $this->fail($model->errors);
    }

    /**
     * Updates an existing Promotion model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws HttpException if the model cannot be found
     */
    public function actionUpdate()
    {
        $model = new Promotion();

        if ($model->updateData(Yii::$app->request->post())) {
            return $this->success();
        }

        return $this->fail($model->errors);
    }

    /**
     * Deletes an existing Promotion model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws HttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->success();
    }

    /**
     * Finds the Promotion model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Promotion the loaded model
     * @throws HttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Promotion::findOne($id)) !== null) {
            return $model;
        }

        throw new HttpException(200,Yii::t('base',40001));
    }
}
