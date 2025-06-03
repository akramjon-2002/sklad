<?php

namespace app\controllers;

use Yii;
use app\models\Sale;
use app\models\Product;
use app\models\QuickSaleForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\data\ActiveDataProvider;

/**
 * SaleController implements the CRUD actions for Sale model.
 */
class SaleController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Sale models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Sale::find()->with('product')->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Quick sale interface
     * @return mixed
     */
    public function actionQuick()
    {
        $model = new QuickSaleForm();

        if ($model->load(Yii::$app->request->post()) && $model->processSale()) {
            Yii::$app->session->setFlash('success', 'Продажа успешно оформлена');
            return $this->redirect(['quick']);
        }

        return $this->render('quick', [
            'model' => $model,
        ]);
    }

    /**
     * Process quick sale via AJAX
     * @return array
     */
    public function actionProcessQuick()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $model = new QuickSaleForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->processSale()) {
            return [
                'success' => true,
                'message' => 'Продажа успешно оформлена',
                'total' => $model->getTotalAmount(),
                'product' => $model->getProduct()->name,
            ];
        }

        return [
            'success' => false,
            'errors' => $model->getErrors(),
        ];
    }

    /**
     * Displays a single Sale model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Sale model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Sale();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Продажа успешно создана');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Sale model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Продажа успешно обновлена');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Sale model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Restore stock
        $model->product->addStock($model->quantity);
        
        $model->delete();
        Yii::$app->session->setFlash('success', 'Продажа успешно удалена');

        return $this->redirect(['index']);
    }

    /**
     * Get sales statistics
     * @return array
     */
    public function actionStats()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');
        
        $todaySales = Sale::find()
            ->where(['>=', 'created_at', $today . ' 00:00:00'])
            ->sum('total_amount') ?: 0;
            
        $monthSales = Sale::find()
            ->where(['>=', 'created_at', $thisMonth . '-01 00:00:00'])
            ->sum('total_amount') ?: 0;
            
        $todayCount = Sale::find()
            ->where(['>=', 'created_at', $today . ' 00:00:00'])
            ->count();
            
        return [
            'today_sales' => $todaySales,
            'month_sales' => $monthSales,
            'today_count' => $todayCount,
        ];
    }

    /**
     * Finds the Sale model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Sale the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Sale::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрашиваемая страница не существует.');
    }
}