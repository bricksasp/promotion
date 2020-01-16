<?php

namespace bricksasp\promotion\controllers;

use Yii;
use bricksasp\promotion\models\Promotion;
use bricksasp\promotion\models\PromotionConditions;
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

    public function actionIndex()
    {
        $params = Yii::$app->request->queryParams;
        $map = [];
        if (isset($params['type'])) {
            $map['type'] = $params['type'];
        }
        $dataProvider = new ActiveDataProvider([
            'query' => Promotion::find($this->dataOwnerUid())->where($map),
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
        $data['conditions'] = $model->conditions ?? (object)[];
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
        $params = Yii::$app->request->post();
        $model = $this->findModel($params['id']);

        if ($model->updateData($params)) {
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
    public function actionDelete()
    {
        $id = Yii::$app->request->get('id');
        Promotion::deleteAll(['id'=>$id]);
        PromotionConditions::deleteAll(['promotion_id'=> $id]);
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
