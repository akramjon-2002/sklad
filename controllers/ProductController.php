<?php

namespace app\controllers;

use Yii;
use app\models\Product;
use app\models\ProductSearch;
use app\models\Category;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use Picqer\Barcode\BarcodeGeneratorPNG;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
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
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Product model.
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
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Product();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Товар успешно создан');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'categories' => Category::find()->all(),
        ]);
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Товар успешно обновлен');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'categories' => Category::find()->all(),
        ]);
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Товар успешно удален');

        return $this->redirect(['index']);
    }

    /**
     * Generate barcode image
     * @param integer $id
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionBarcode($id)
    {
        $model = $this->findModel($id);
        
        if (empty($model->barcode)) {
            throw new NotFoundHttpException('Штрихкод не найден');
        }

        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($model->barcode, $generator::TYPE_CODE_128, 3, 50);

        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'image/png');
        
        return $barcode;
    }

    /**
     * Search products by barcode (AJAX)
     * @return array
     */
    public function actionSearchByBarcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $barcode = Yii::$app->request->get('barcode');
        if (empty($barcode)) {
            return ['success' => false, 'message' => 'Штрихкод не указан'];
        }

        $product = Product::find()->where(['barcode' => $barcode])->one();
        if (!$product) {
            return ['success' => false, 'message' => 'Товар не найден'];
        }

        return [
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'barcode' => $product->barcode,
                'price_per_unit' => $product->price_per_unit,
                'current_stock' => $product->current_stock,
                'unit_type' => $product->unit_type,
                'unit_type_label' => $product->getUnitTypeLabel(),
            ]
        ];
    }

    /**
     * Get products list for AJAX
     * @return array
     */
    public function actionList()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $query = Yii::$app->request->get('q', '');
        $products = Product::find()
            ->where(['ilike', 'name', $query])
            ->orWhere(['ilike', 'barcode', $query])
            ->limit(20)
            ->all();

        $results = [];
        foreach ($products as $product) {
            $results[] = [
                'id' => $product->id,
                'text' => $product->name . ' (' . $product->barcode . ')',
                'barcode' => $product->barcode,
                'price' => $product->price_per_unit,
                'stock' => $product->current_stock,
                'unit' => $product->getUnitTypeLabel(),
            ];
        }

        return ['results' => $results];
    }

    /**
     * Get product by barcode (AJAX endpoint)
     * @param string $barcode
     * @return array
     */
    public function actionGetByBarcode($barcode)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $product = Product::find()
            ->with('category')
            ->where(['barcode' => $barcode])
            ->one();
            
        if ($product) {
            return [
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'barcode' => $product->barcode,
                    'price' => $product->price_per_unit,
                    'stock' => $product->current_stock,
                    'category_name' => $product->category ? $product->category->name : null,
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Товар не найден'
            ];
        }
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрашиваемая страница не существует.');
    }
}