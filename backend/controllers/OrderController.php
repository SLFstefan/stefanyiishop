<?php
namespace backend\controllers;
use backend\filters\AccessFilter;
use frontend\models\Order;
use yii\data\Pagination;
use yii\web\Controller;

class OrderController extends Controller{
    //订单详情首页
    public function actionIndex(){
        //获取所有的订单
        $order_page=Order::find();
        $total=$order_page->count();
        //配置分页
        $page=new Pagination([
            'totalCount'=>$total,
            'defaultPageSize'=>6,
        ]);
        //获取每页的数据
        $orders=$order_page->offset($page->offset)->orderBy('create_time')->limit($page->limit)->all();
        //渲染首页视图
        return $this->render('index',['orders'=>$orders,'page'=>$page]);
    }
    //发货
    public function actionSend($id){
        //根据ID找到对应的订单
        $order=Order::findOne(['id'=>$id]);
        //修改状态
        $order->status=3;
        $order->save();
        //提示信息
        \Yii::$app->session->setFlash('success','发货成功，等待收货');
        //返回首页
        return $this->redirect(['order/index']);
    }
    //删除订单
    public function actionDel($id){
        //根据ID找到对应的订单
        $order=Order::findOne(['id'=>$id]);
        //删除
        $order->delete();
        //提示信息
        \Yii::$app->session->setFlash('success','清除完成订单成功');
        //返回首页
        return $this->redirect(['order/index']);
    }
    //RBAC授权
    public function behaviors(){
        return [
            'accessFilter'=>[
                'class'=>AccessFilter::className(),
            ]
        ];
    }
}