<?php

namespace app\controllers;

use Yii;
use app\models\Product;
use app\models\Sale;
use app\models\Income;
use yii\web\Controller;
use yii\web\Response;

/**
 * ReportController implements reporting functionality.
 */
class ReportController extends Controller
{
    /**
     * Dashboard with main statistics
     * @return mixed
     */
    public function actionIndex()
    {
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');
        
        // Today's sales
        $todaySales = Sale::find()
            ->where(['>=', 'created_at', $today . ' 00:00:00'])
            ->sum('total_amount') ?: 0;
            
        $todayCount = Sale::find()
            ->where(['>=', 'created_at', $today . ' 00:00:00'])
            ->count();
            
        // This month's sales
        $monthSales = Sale::find()
            ->where(['>=', 'created_at', $thisMonth . '-01 00:00:00'])
            ->sum('total_amount') ?: 0;
            
        // Low stock products
        $lowStockProducts = Product::find()
            ->where(['<=', 'current_stock', 10])
            ->andWhere(['>', 'current_stock', 0])
            ->orderBy(['current_stock' => SORT_ASC])
            ->limit(10)
            ->all();
            
        // Out of stock products
        $outOfStockProducts = Product::find()
            ->where(['current_stock' => 0])
            ->count();
            
        // Total products
        $totalProducts = Product::find()->count();
        
        // Recent sales
        $recentSales = Sale::find()
            ->with('product')
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(10)
            ->all();

        return $this->render('index', [
            'todaySales' => $todaySales,
            'todayCount' => $todayCount,
            'monthSales' => $monthSales,
            'lowStockProducts' => $lowStockProducts,
            'outOfStockProducts' => $outOfStockProducts,
            'totalProducts' => $totalProducts,
            'recentSales' => $recentSales,
        ]);
    }

    /**
     * Sales report
     * @return mixed
     */
    public function actionSales()
    {
        $dateFrom = Yii::$app->request->get('date_from', date('Y-m-01'));
        $dateTo = Yii::$app->request->get('date_to', date('Y-m-d'));
        
        $query = Sale::find()
            ->with('product')
            ->where(['>=', 'created_at', $dateFrom . ' 00:00:00'])
            ->andWhere(['<=', 'created_at', $dateTo . ' 23:59:59'])
            ->orderBy(['created_at' => SORT_DESC]);
            
        $sales = $query->all();
        $totalAmount = $query->sum('total_amount') ?: 0;
        $totalCount = $query->count();

        return $this->render('sales', [
            'sales' => $sales,
            'totalAmount' => $totalAmount,
            'totalCount' => $totalCount,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    /**
     * Stock report
     * @return mixed
     */
    public function actionStock()
    {
        $products = Product::find()
            ->with('category')
            ->orderBy(['current_stock' => SORT_ASC])
            ->all();

        return $this->render('stock', [
            'products' => $products,
        ]);
    }

    /**
     * Income report
     * @return mixed
     */
    public function actionIncome()
    {
        $dateFrom = Yii::$app->request->get('date_from', date('Y-m-01'));
        $dateTo = Yii::$app->request->get('date_to', date('Y-m-d'));
        
        $query = Income::find()
            ->with('product')
            ->where(['>=', 'created_at', $dateFrom . ' 00:00:00'])
            ->andWhere(['<=', 'created_at', $dateTo . ' 23:59:59'])
            ->orderBy(['created_at' => SORT_DESC]);
            
        $incomes = $query->all();
        $totalCost = $query->sum('total_cost') ?: 0;
        $totalCount = $query->count();

        return $this->render('income', [
            'incomes' => $incomes,
            'totalCost' => $totalCost,
            'totalCount' => $totalCount,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    /**
     * Export sales to CSV
     * @return Response
     */
    public function actionExportSales()
    {
        $dateFrom = Yii::$app->request->get('date_from', date('Y-m-01'));
        $dateTo = Yii::$app->request->get('date_to', date('Y-m-d'));
        
        $sales = Sale::find()
            ->with('product')
            ->where(['>=', 'created_at', $dateFrom . ' 00:00:00'])
            ->andWhere(['<=', 'created_at', $dateTo . ' 23:59:59'])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        $filename = 'sales_' . $dateFrom . '_' . $dateTo . '.csv';
        
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'text/csv; charset=utf-8');
        Yii::$app->response->headers->add('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // BOM for UTF-8
        fwrite($output, "\xEF\xBB\xBF");
        
        // Header
        fputcsv($output, ['Дата', 'Товар', 'Количество', 'Цена за единицу', 'Общая сумма', 'Примечания'], ';');
        
        // Data
        foreach ($sales as $sale) {
            fputcsv($output, [
                $sale->created_at,
                $sale->product->name,
                $sale->quantity,
                $sale->price_per_unit,
                $sale->total_amount,
                $sale->notes,
            ], ';');
        }
        
        fclose($output);
        
        return Yii::$app->response;
    }

    /**
     * Get sales chart data
     * @return array
     */
    public function actionSalesChart()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $days = Yii::$app->request->get('days', 7);
        $data = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $amount = Sale::find()
                ->where(['>=', 'created_at', $date . ' 00:00:00'])
                ->andWhere(['<=', 'created_at', $date . ' 23:59:59'])
                ->sum('total_amount') ?: 0;
                
            $data[] = [
                'date' => $date,
                'amount' => (float)$amount,
            ];
        }
        
        return $data;
    }
}