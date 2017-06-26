<?php

namespace frontend\controllers;

use backend\models\Goods;
use backend\models\GoodsCategory;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\Locations;
use frontend\models\LoginForm;
use frontend\models\Member;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii\data\Pagination;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;

class MemberController extends \yii\web\Controller
{
    //加载布局文件
    public $layout;
    //注册
    public function actionRegister(){
        $this->layout='login';
        $model=new Member(['scenario'=>Member::SCENARIO_REGISTER]);
        if($model->load(\Yii::$app->request->post())){
            if($model->validate()){
                //生成注册时间
                $model->created_at=time();
                //密码加密
                $model->password_hash=\Yii::$app->security->generatePasswordHash($model->password);
                //保存默认状态
                $model->status=1;
                //保存
                $model->save(false);
                //跳转到登录界面
                return $this->redirect(['member/login']);
            }else{
                var_dump($model->getErrors());
                exit;
            }

        }
        return $this->render('register',['model'=>$model]);
    }
    public function actionIndex()
    {
        $this->layout='index';
        return $this->render('index');
    }
    //登录
    public function actionLogin(){
        $this->layout='login';
        $model=new LoginForm();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //根据用户名查找对应的用户
            $member=Member::findOne(['username'=>$model->username]);
            //生成最后登录时间
            $member->last_login_time=time();
            //生成最后登录IP
            $member->last_login_ip=\Yii::$app->request->getUserIP();
            //保存
            $member->save(false);
            //用户登录成功，将cookie中的数据同步到数据表
            //从cookie中读取数据
            $cookies=\Yii::$app->request->cookies;
            $cookie=$cookies->get('cart');
            if($cookie==null){
                $cart=[];
            }else{
                $cart=unserialize($cookie->value);
            }
            //循环读取cookie中的购物车数据
            foreach ($cart as $goods_id=>$number){
                $car=Cart::findOne(['goods_id'=>$goods_id,'member_id'=>\Yii::$app->user->identity->getId()]);
                if($car){
                    $car->amount+=$number;
                    $car->save();
                }else{
                    $car=new Cart();
                    $car->member_id=\Yii::$app->user->identity->getId();
                    $car->goods_id=$goods_id;
                    $car->amount=$number;
                    $car->save();
                }
            }
            //同步成功，清除cookie中的数据
            $cookies=\Yii::$app->response->cookies;
            $cookies->remove('cart');
            //跳转到用户界面
            return $this->redirect(['member/index']);
        }
        return $this->render('login',['model'=>$model]);
    }
    //退出登录
    public function actionLogout(){
        //退出
        \Yii::$app->user->logout();
        //跳转到首页界面
        return $this->redirect(['member/index']);
    }
    //收货地址
    public function actionAddress(){
        $this->layout='index';
        //新建地址模型对象
        $model=new Address();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //关联用户
            $model->member_id=\Yii::$app->user->identity->id;
            //保存
            $model->save();
            //返回地址页
            return $this->redirect(['member/address']);
        }
        //查询数据库的所有地址
        $addresses=Address::find()->where(['member_id'=>\Yii::$app->user->id])->orderBy('status DESC')->all();
        return $this->render('address',['addresses'=>$addresses,'model'=>$model]);
    }
    //更新地址
    public function actionUpdateAddress($id){
        $this->layout='index';
        //通过ID查找对应地址
        $model=Address::findOne(['id'=>$id]);
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //保存
            $model->save();
            //返回地址页
            return $this->redirect(['member/address']);
        }
        //查询数据库的当前登录用户的所有地址

        $addresses=Address::find()->where(['member_id'=>\Yii::$app->user->id])->orderBy('status DESC')->all();
        return $this->render('address',['addresses'=>$addresses,'model'=>$model]);

    }
    //删除地址
    public function actionDelAddress($id){
        $this->layout='index';
        //通过ID查找对应的地址
        $model=Address::findOne(['id'=>$id]);
        //删除
        $model->delete();
        //返回地址页
        return $this->redirect(['member/address']);
    }
    //设置默认地址
    public function actionDefaultAddress($id){
        $this->layout='index';
        //通过ID查找对应的地址
        //var_dump($id);exit;
        $model=Address::findOne(['id'=>$id]);
        //判断当前登录用户是否已经有默认地址
        $default=Address::findOne(['status'=>1,'member_id'=>\Yii::$app->user->id]);
        if($default){
            $default->status=0;
            $default->save();
        }
        $model->status=1;
        $model->save();
        return $this->redirect(['member/address']);

    }
    //ajax读取省市区
    public function actionRead(){
        $id=\Yii::$app->request->get('id');
        $locations=Locations::find()->where(['parent_id'=>$id])->asArray()->all();
        return json_encode($locations);
    }
    //商品列表
    public function actionList($cate_id){
        $this->layout='goods';
        //新建商品模型对象
        $goods_page=Goods::find();
        $total=$goods_page->count();
        //配置分页
        $page=new Pagination([
            'totalCount'=>$total,
            'defaultPageSize'=>8,
        ]);
        //根据ID查找分类对象
        $category=GoodsCategory::findOne(['id'=>$cate_id]);
        //获取该分类对象下面的所有子分类
        $categories=GoodsCategory::find()->where(['>=','lft',$category->lft])->andWhere(['<=','rgt',$category->rgt])->andWhere(['tree'=>$category->tree])->all();
        //获取分类的ID
        $cateIds = ArrayHelper::map($categories,'id','id');
        //查询数据
        $goods=$goods_page->offset($page->offset)->where(['in','goods_category_id',$cateIds])->limit($page->limit)->all();
        /*foreach ($categories as $cate){
            //获取每页对应的商品
            $goods[]=$goods_page->offset($page->offset)->where(['goods_category_id'=>$cate->id])->limit($page->limit)->all();
        }*/
        //var_dump($goods);exit;
        return $this->render('list',['goods'=>$goods,'page'=>$page]);
    }
    //商品详情
    public function actionGoods($goods_id){
        $this->layout='goods';
        //通过goods_id查找对应的商品
        $goods=Goods::findOne(['id'=>$goods_id]);
        return $this->render('goods',['goods'=>$goods]);
    }
    //发送短信验证码
    public function actionSendSms()
    {
        //确保上一次发送短信间隔超过1分钟
        $tel = \Yii::$app->request->post('tel');
        if(!preg_match('/^1[34578]\d{9}$/',$tel)){
            echo '电话号码不正确';
            exit;
        }
        $code = rand(10000,99999);
        $result = \Yii::$app->sms->setNum($tel)->setParam(['code' => $code])->send();
        //$result = 1;
        if($result){
            //保存当前验证码 session  mysql  redis  不能保存到cookie
            //\Yii::$app->session->set('code',$code);
            //\Yii::$app->session->set('tel_'.$tel,$code);
            \Yii::$app->cache->set('tel_'.$tel,$code,5*60);
            echo 'success'.$code;
        }else{
            echo '发送失败';
        }
    }
    //发送邮件
    public function actionSendEmail(){
        //通过邮箱重设密码
        $result = \Yii::$app->mailer->compose()
            ->setFrom('slfstefan@qq.com')//谁的邮箱发出的邮件
            ->setTo('slfstefan@qq.com')//发给谁
            ->setSubject('岁月如歌')//邮件的主题
            //->setTextBody('Plain text content')//邮件的内容text格式
            ->setHtmlBody('<b style="color: lightseagreen">岁月如歌，一曲终了，总有人不愿散场</b>')//邮件的内容 html格式
            ->send();
        var_dump($result);
    }
    //添加商品到购物车
    public function actionAddCart(){
        //接收商品ID和商品数量
        $goods_id=\Yii::$app->request->post('goods_id');
        $number=\Yii::$app->request->post('number');
        //根据商品ID查找对应商品
        $goods=Goods::findOne(['id'=>$goods_id]);
        if($goods==null){
            throw new NotFoundHttpException('无相关商品');
        }
        //判断用户是否登录
        if(\Yii::$app->user->isGuest){
            //实例化cookie
            $cookies=\Yii::$app->request->cookies;
            //先获取cookie中的购物车数据
            $cookie=$cookies->get('cart');
            if($cookie==null){
                //cookie中无购物车数据
                $cart=[];
            }else{
                $cart=unserialize($cookie->value);
            }
            //将新增的商品和之前购物车中的商品合并
            $cookies=\Yii::$app->response->cookies;
            //检查购物车中是否有改商品
            if(key_exists($goods->id,$cart)){
                $cart[$goods_id]+=$number;
            }else{
                $cart[$goods_id]=$number;
            }
            //保存到cookie中
            $cookie=new Cookie([
                'name'=>'cart','value'=>serialize($cart)
                ]
            );
            $cookies->add($cookie);
        }else{
            //已登录
            //判断收据库中是否有该用户买了该商品
            $model=Cart::findOne(['goods_id'=>$goods_id,'member_id'=>\Yii::$app->user->identity->getId()]);
            if($model){
                $model->amount=$model->amount+$number;
                $model->save();
            }else{
                $model=new Cart();
                $model->amount=$number;
                $model->goods_id=$goods_id;
                $model->member_id=\Yii::$app->user->identity->getId();
                $model->save();
            }
        }
        return $this->redirect(['member/cart']);
    }
    //购物车
    public function actionCart(){
        $this->layout='cart';
        //判断是否登录
        if(\Yii::$app->user->isGuest){
            //从cookie中读取数据
            $cookies=\Yii::$app->request->cookies;
            $cookie=$cookies->get('cart');
            if($cookie==null){
                $cart=[];
            }else{
                $cart=unserialize($cookie->value);
            }
            $models=[];
            //循环读取cookie中的数据,在购物车中显示
            foreach ($cart as $goods_id=>$number){
                $goods=Goods::findOne(['id'=>$goods_id])->attributes;
                $goods['number']=$number;
                $models[]=$goods;
            }
        }else{
            //已登录
            //读取数据库中所有的购物车数据
            $carts=Cart::find()->where(['member_id'=>\Yii::$app->user->identity->getId()])->all();
            //遍历
            $models=[];
            foreach ($carts as $car){
                //根据购物车的商品ID查找对应商品
                $good=Goods::findOne(['id'=>$car->goods_id])->attributes;
                $good['number']=$car->amount;
                $models[]=$good;
            }
        }
        return $this->render('cart',['models'=>$models]);
    }
    //更新购物车
    public function actionUpdateCart(){
        //接收商品ID和商品数量
        $goods_id=\Yii::$app->request->post('goods_id');
        $number=\Yii::$app->request->post('number');
        //根据商品ID查找对应商品
        $goods=Goods::findOne(['id'=>$goods_id]);
        if($goods==null){
            throw new NotFoundHttpException('无相关商品');
        }
        //判断用户是否登录
        if(\Yii::$app->user->isGuest){
            //实例化cookie
            $cookies=\Yii::$app->request->cookies;
            //先获取cookie中的购物车数据
            $cookie=$cookies->get('cart');
            if($cookie==null){
                //cookie中无购物车数据
                $cart=[];
            }else{
                $cart=unserialize($cookie->value);
            }
            //将新增的商品和之前购物车中的商品合并
            $cookies=\Yii::$app->response->cookies;
            //判断商品数量是否为零,为零则删除该商品
            if($number){
                $cart[$goods_id]=$number;
            }else{
                if(key_exists($goods->id,$cart))unset($cart[$goods_id]);
            }

            //保存到cookie中
            $cookie=new Cookie([
                    'name'=>'cart','value'=>serialize($cart)
                ]
            );
            $cookies->add($cookie);
        }else{
            //已登录
            //根据用户ID和商品ID查找对应的记录
            $car=Cart::findOne(['goods_id'=>$goods_id,'member_id'=>\Yii::$app->user->identity->getId()]);
            if($number){
                //跟新数据记录
                $car->amount=$number;
                $car->save();
            }else{
                //删除记录
                $car->delete();
            }
        }
    }
    //订单
    public function actionOrder(){
        $this->layout='order';
        $model=new Order();
        //读取该用户的所有购物车数据
        $carts=Cart::find()->where(['member_id'=>\Yii::$app->user->id])->all();
        //读取所有收货地址信息
        $addresses=Address::find()->all();
        return $this->render('order',['model'=>$model,'addresses'=>$addresses,'carts'=>$carts]);
    }
    //添加订单
    public function actionAddOrder(){
        $order=New Order();
        $delivery_id=\Yii::$app->request->post('delivery_id');
        $address_id=\Yii::$app->request->post('address_id');
        $payment_id=\Yii::$app->request->post('payment_id');
        $total_money=\Yii::$app->request->post('total_money');
        //根据送货方式ID查询送货方式
        foreach (Order::$delivery_options as $deliverys){
            if($deliverys['id']==$delivery_id){
                $delivery=$deliverys;
            }
        }
        //根据支付方式ID查询支付方式
        foreach (Order::$payment_options as $payments){
            if($payments['id']==$payment_id){
                $payment=$payments;
            }
        }
        //根据收货地址ID查询地址
        $address=Address::findOne(['id'=>$address_id,'member_id'=>\Yii::$app->user->id]);
        //保存订单表
        if($order->validate()){
            $order->member_id=\Yii::$app->user->identity->getId();
            $order->name=$address->name;
            $order->province=$address->province->name;
            $order->city=$address->city->name;
            $order->area=$address->area->name;
            $order->address=$address->detail_address;
            $order->tel=$address->phone;
            $order->delivery_id=$delivery['id'];
            $order->delivery_name=$delivery['name'];
            $order->delivery_price=$delivery['price'];
            $order->payment_id=$payment['id'];
            $order->payment_name=$payment['name'];
            $order->total=$total_money;
            if($delivery['id']==1){
                $order->status=2;
            }else{
                $order->status=1;
            }
            $order->create_time=time();
            //开启事务
            $transaction=\Yii::$app->db->beginTransaction();
            try{
                $order->save();
                //保存订单商品表
                //根据用户ID找到购物车的记录
                $carts=Cart::find()->where(['member_id'=>\Yii::$app->user->id])->all();
                foreach ($carts as $cart){
                    $order_goods=new OrderGoods();
                    if($order_goods->validate()){
                        $order_goods->order_id=$order->id;
                        $goods=Goods::findOne(['id'=>$cart->goods_id]);
                        if($goods==null){
                            throw new Exception('该商品已下架');
                        }
                        if($goods->stock<$cart->amount){
                            throw new Exception('该商品库存不足');
                        }
                        $order_goods->goods_id=$cart->goods_id;
                        $order_goods->goods_name=$goods->name;
                        $order_goods->logo=$goods->logo;
                        $order_goods->price=$goods->shop_price;
                        $order_goods->amount=$cart->amount;
                        $order_goods->total=$cart->amount*$goods->shop_price;
                        $order_goods->save();
                        //商品库存对应减少
                        $goods->stock-=$cart->amount;
                        $goods->save();
                        //清除购物车记录
                        $cart->delete();
                    }else{
                        var_dump($order_goods->getErrors());
                        exit;
                    }
                }
                //提交
                $transaction->commit();
            }catch (Exception $e){
                //回滚
                $transaction->rollBack();
            }

        }else{
            var_dump($order->getErrors());
            exit;
        }
    }
    //提交订单成功
    public function actionSuccess(){
        $this->layout='success';
        return $this->render('success');
    }
    //我的订单
    public function actionMyOrder(){
        $this->layout='index';
        //获取当前用户的订单
        $orders=Order::find()->where(['member_id'=>\Yii::$app->user->id])->all();
        return $this->render('my-order',['orders'=>$orders]);
    }
}
