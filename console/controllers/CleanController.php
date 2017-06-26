<?php
namespace console\controllers;
use backend\models\Goods;
use frontend\models\Order;
use yii\console\Controller;

class CleanController extends Controller{
    public function actionClean(){
        //不限制脚本执行时间
        while(1){
            set_time_limit(0);
            //状态1=>待付款==》状态0=>已取消 超过一小时未付款
            $orders=Order::find()->where(['status'=>1])->andWhere(['<','create_time',time()-3600])->all();
            //更改状态
            foreach ($orders as $order){
                $order->status=0;
                $order->save();
                //返还库存
                foreach ($order->goods as $Order_goods){
                    $goods=Goods::findOne(['id'=>$Order_goods->goods_id]);
                    $goods->stock+=$Order_goods->amount;
                }
            }
        }
        sleep(60);
    }
}