<?php
namespace frontend\controllers;
use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\Member;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii\captcha\Captcha;
use yii\captcha\CaptchaAction;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\Response;
use yii\web\UploadedFile;

class ApiController extends Controller{
    //关闭跨站访问验证
    public $enableCsrfValidation=false;
    public function init()
    {
        //指定数据响应格式
        \Yii::$app->response->format=Response::FORMAT_JSON;
        parent::init();
    }
    //会员登录
    public function actionLogin(){
        $request=\Yii::$app->request;
        if($request->isPost){
            $member=Member::findOne(['username'=>\Yii::$app->request->post('username')]);
            if($member && \Yii::$app->security->validatePassword(\Yii::$app->request->post('password'),$member->password_hash)){
                \Yii::$app->user->login($member);
                return ['status'=>1,'msg'=>'登录成功'];
            }
            return ['status'=>-1,'msg'=>'用户名或密码错误'];
        }
        return ['status'=>-1,'msg'=>'请使用post请求'];
    }
    //用户注册
    public function actionRegister(){
        $request=\Yii::$app->request;
        if($request->isPost){
            $member=new Member();
            $member->scenario = Member::SCENARIO_API_REGISTER;
            $member->username=\Yii::$app->request->post('username');
            $member->password_hash=\Yii::$app->security->generatePasswordHash(\Yii::$app->request->post('password'));
            $member->email=\Yii::$app->request->post('email');
            $member->tel=\Yii::$app->request->post('tel');
            $member->created_at=time();
            $member->status=0;
            $member->code=$request->post('code');
            if($member->validate()){
                $member->save();
                return ['status'=>1,'msg'=>'','data'=>$member->toArray()];
            }
            return ['status'=>-1,'msg'=>$member->getErrors()];
        }
        return ['status'=>-1,'msg'=>'请使用post请求'];
    }
    //修改密码
    public function actionEdit(){
        $request=\Yii::$app->request;
        if($request->isPost) {
            if (!\Yii::$app->user->isGuest) {
                $member = Member::findOne(['id' => \Yii::$app->user->id]);
                $old_password = \Yii::$app->request->post('old_password');
                if (\Yii::$app->security->validatePassword($old_password, $member->password_hash)) {
                    if (\Yii::$app->request->post('new_password') == \Yii::$app->request->post('repassword')) {
                        $member->password_hash = \Yii::$app->security->generatePasswordHash(\Yii::$app->request->post('new_password'));
                        $member->save();
                        return ['status' => 1, 'msg' => '', 'new_password' => \Yii::$app->request->post('new_password')];
                    }
                    return ['status' => -1, 'msg' => '新密码与确认密码不一致'];
                }
                return ['status' => '-1', 'msg' => '旧密码错误'];
            }
            return ['status'=>-1,'msg'=>'请先登录'];
        }
        return ['status'=>-1,'msg'=>'请使用post请求'];
    }
    //获取当前登录用户的信息
    public function actionGetLoginUser(){
        if(\Yii::$app->user->isGuest){
            return ['status'=>-1,'msg'=>'请先登录'];
        }
        return ['status'=>1,'msg'=>'','data'=>\Yii::$app->user->identity->toArray()];
    }
    //添加地址
    public function actionAddAddress(){
        $request=\Yii::$app->request;
        if(!\Yii::$app->user->isGuest){
            if($request->isPost){
                $address=new Address();
                $address->name=\Yii::$app->request->post('name');
                $address->province_id=\Yii::$app->request->post('province_id');
                $address->city_id=\Yii::$app->request->post('city_id');
                $address->area_id=\Yii::$app->request->post('area_id');
                $address->detail_address=\Yii::$app->request->post('detail_address');
                $address->phone=\Yii::$app->request->post('phone');
                $address->status=\Yii::$app->request->post('status');
                $address->member_id=\Yii::$app->user->id;
                if($address->validate()){
                    $address->save();
                    return['status'=>1,'msg'=>'','data'=>$address->toArray()];
                }
                return ['status'=>-1,'msg'=>$address->getErrors()];
            }
            return ['status'=>-1,'msg'=>'请使用post请求'];
        }
        return ['status'=>-1,'msg'=>'请先登录'];
    }
    //修改地址
    public function actionEditAddress(){
        $request=\Yii::$app->request;
        if(!\Yii::$app->user->isGuest){
            if($request->isPost){
                $address=Address::findOne(['id'=>\Yii::$app->request->post('address_id')]);
                if($address){
                    $address->name=\Yii::$app->request->post('name');
                    $address->province_id=\Yii::$app->request->post('province_id');
                    $address->city_id=\Yii::$app->request->post('city_id');
                    $address->area_id=\Yii::$app->request->post('area_id');
                    $address->detail_address=\Yii::$app->request->post('detail_address');
                    $address->phone=\Yii::$app->request->post('phone');
                    $address->status=\Yii::$app->request->post('status');
                    if($address->validate()){
                        $address->save();
                        return['status'=>1,'msg'=>'','data'=>$address->toArray()];
                    }
                    return ['status'=>-1,'msg'=>$address->getErrors()];
                }
                return ['status'=>-1,'msg'=>'无相关地址'];
            }
            return ['status'=>-1,'msg'=>'请使用post请求'];
        }
        return ['status'=>-1,'msg'=>'请先登录'];
    }
    //删除地址
    public function actionDelAddress(){
        $request=\Yii::$app->request;
        if(!\Yii::$app->user->isGuest){
            $address=Address::findOne(['id'=>$request->get('address_id')]);
            if($address){
                $address->delete();
                return ['status'=>1,'msg'=>'删除地址成功'];
            }
            return ['status'=>-1,'msg'=>'无相关地址'];
        }
        return ['status'=>-1,'msg'=>'请先登录'];
    }
    //地址列表
    public function actionListAddress(){
        if (!\Yii::$app->user->isGuest) {
            $address=Address::find()->where(['member_id'=>\Yii::$app->user->id])->asArray()->all();
            return ['status'=>1,'msg'=>'','data'=>$address];
        }
        return ['status'=>-1,'msg'=>'请先登录'];
    }
    //获取所有的商品分类
    public function actionCategory(){
        $categories=GoodsCategory::find()->all();
        if($categories){
            return ['status'=>1,'msg'=>'','data'=>$categories];
        }
        return ['status'=>-1,'msg'=>'无商品分类'];
    }
    //获取某分类的所有子分类
    public function actionChildren(){
        $request=\Yii::$app->request;
        $category=GoodsCategory::findOne(['id'=>$request->get('category_id')]);
        if($category){
            $children=GoodsCategory::find()->where(['>','lft',$category->lft])->andWhere(['<','rgt',$category->rgt])->andWhere(['tree'=>$category->tree])->asArray()->all();
            if($children){
                return ['status'=>1,'msg'=>'','data'=>$children];
            }
            return ['status'=>-1,'msg'=>'该分类无子分类'];
        }
        return ['status'=>-1,'msg'=>'无该商品分类'];
    }
    //获取某分类的父分类
    public function actionParent(){
        $request=\Yii::$app->request;
        $category=GoodsCategory::findOne(['id'=>$request->get('category_id')]);
        if($category){
            $parent=GoodsCategory::findOne(['id'=>$category->parent_id]);
            if($parent){
                return ['status'=>1,'msg'=>'','data'=>$parent->attributes];
            }
            return ['status'=>-1,'msg'=>'该分类是顶级分类'];
        }
        return ['status'=>-1,'msg'=>'无该商品分类'];
    }
    //获取某分类下面的所有商品
    public function actionGoods(){
        $request=\Yii::$app->request;
        $goods_page=Goods::find();
        //每页显示条数
        $per_page = \Yii::$app->request->get('per_page',2);
        //当前第几页
        $page = \Yii::$app->request->get('page',1);
        //搜索条件
        $keywords = \Yii::$app->request->get('keywords');
        $page = $page < 1?1:$page;
        $category=GoodsCategory::findOne(['id'=>$request->get('category_id')]);
        if($category){
            $categories=GoodsCategory::find()->where(['>=','lft',$category->lft])->andWhere(['<=','rgt',$category->rgt])->andWhere(['tree'=>$category->tree])->asArray()->all();
            $cates=ArrayHelper::map($categories,'id','id');
            $goods_page->andWhere(['in','goods_category_id',$cates]);
            if($keywords){
                $goods_page->andWhere(['like','name',$keywords]);
            }
            //总条数
            $total = $goods_page->count();
            $goods=$goods_page->offset($per_page*($page-1))->limit($per_page)->asArray()->all();
            if($goods){
                return ['status'=>1,'msg'=>'','data'=>[
                    'total'=>$total,
                    'per_page'=>$per_page,
                    'page'=>$page,
                    'goods'=>$goods
                ]];
            }
            return ['status'=>-1,'msg'=>'该分类下没有商品'];
        }
        return ['status'=>-1,'msg'=>'无该商品分类'];
    }
    //获取某品牌下的所有商品
    public function actionBrandGoods(){
        $request=\Yii::$app->request;
        $goods_page=Goods::find();
        //每页显示条数
        $per_page = \Yii::$app->request->get('per_page',2);
        //当前第几页
        $page = \Yii::$app->request->get('page',1);
        //搜索条件
        $keywords = \Yii::$app->request->get('keywords');
        $page = $page < 1?1:$page;
        $brand=Brand::findOne(['id'=>$request->get('brand_id')]);
        if($brand){
            $goods_page->andWhere(['brand_id'=>$brand->id]);
            if($keywords){
                $goods_page->andWhere(['like','name',$keywords]);
            }
            //总条数
            $total = $goods_page->count();
            $goods=$goods_page->offset($per_page*($page-1))->limit($per_page)->asArray()->all();
            if($goods){
                return ['status'=>1,'msg'=>'','data'=>[
                    'total'=>$total,
                    'per_page'=>$per_page,
                    'page'=>$page,
                    'goods'=>$goods
                ]];
            }
            return ['status'=>-1,'msg'=>'该品牌无对应商品'];
        }
        return ['status'=>-1,'msg'=>'无该品牌'];
    }
    //获取文章分类
    public function actionArticleCategory(){
        $article_category=ArticleCategory::find()->all();
        if($article_category){
            return ['status'=>1,'msg'=>'','data'=>$article_category];
        }
        return ['status'=>-1,'msg'=>'文章分类'];

    }
    //获取某分类下面的所有文章
    public function actionArticle(){
        $request=\Yii::$app->request;
        $article_category=ArticleCategory::findOne(['id'=>$request->get('article_category_id')]);
        if($article_category){
            $articles=Article::find()->where(['article_category_id'=>$article_category->id])->all();
            if($articles){
                return ['status'=>1,'msg'=>'','data'=>$articles];
            }
            return ['status'=>-1,'msg'=>'该分类无对应文章'];
        }
        return ['status'=>-1,'msg'=>'无该文章分类'];
    }
    //获取某文章所属的分类
    public function actionCategoryArticle(){
        $request=\Yii::$app->request;
        $article=Article::findOne(['id'=>$request->get('article_id')]);
        if($article){
            $category_article=ArticleCategory::findOne(['id'=>$article->article_category_id]);
            if($category_article){
                return ['status'=>1,'msg'=>'','data'=>$category_article->attributes];
            }
            return ['status'=>-1,'msg'=>'改文章无对应分类'];
        }
        return ['status'=>-1,'msg'=>'无该文章'];
    }
    //添加商品到购物车
    public function actionAddCart(){
        $request=\Yii::$app->request;
        if($request->isPost){
            $goods_id=$request->post('goods_id');
            $amount=$request->post('amount');
            $goods=Goods::findOne(['id'=>$goods_id]);
            if($goods){
                //读取原购物车中的商品==》读==》request
                if(\Yii::$app->user->isGuest){
                    //实例化cookie
                    $cookies=\Yii::$app->request->cookies;
                    $cookie=$cookies->get('cart');
                    if($cookie==null){
                        $cart=[];
                    }else{
                        $cart=unserialize($cookie->value);
                    }
                    //将新增的商品和购物车中的商品合并==》写==》response
                    $cookies=\Yii::$app->response->cookies;
                    //查购物车中是否有该商品
                    if(key_exists($goods->id,$cart)){
                        $cart['goods_id']+=$amount;
                    }else{
                        $cart['goods_id']=$amount;
                    }
                    $cookie=new Cookie([
                        'name'=>'cart','value'=>serialize($cart)
                    ]);
                    $cookies->add($cookie);
                    return ['status'=>1,'msg'=>'未登录状态添加成功'];

                }else{
                    //判断该登录用户是否购买了该商品
                    $model=Cart::findOne(['goods_id'=>$goods_id,'member_id'=>\Yii::$app->user->id]);
                    if($model){
                        $model->amount=$amount+$model->amount;
                        $model->save();
                    }else{
                        $model=new Cart();
                        $model->goods_id=$goods_id;
                        $model->amount=$amount;
                        $model->member_id=\Yii::$app->user->id;
                        $model->save();
                    }
                    return ['status'=>1,'msg'=>'登录状态添加成功'];
                }
            }
            return ['status'=>-1,'msg'=>'无相关商品'];
        }
        return ['status'=>-1,'msg'=>'请使用post请求'];
    }
    //删除购物车某商品数量
    public function actionUpdateCart(){
        $request=\Yii::$app->request;
        if($request->isPost){
            $goods_id=$request->post('goods_id');
            $amount=$request->post('amount');
            $goods=Goods::findOne(['id'=>$goods_id]);
            if($goods){
                if(\Yii::$app->user->isGuest){
                    //读取原cookie的信息
                    $cookies=\Yii::$app->request->cookies;
                    $cookie=$cookies->get('cart');
                    if($cookie==null){
                        $cart=[];
                    }else{
                        $cart=unserialize($cookie->value);
                    }
                    //更新cookie的信息
                    $cookies=\Yii::$app->response->cookies;
                    if($amount==0){
                        if(key_exists($goods->id,$cart))unset($cart['goods_id']);
                        return ['status'=>1,'msg'=>'未登录状态删除商品成功'];
                    }else{
                        $cart['goods_id']=$amount;
                        $cookie=new Cookie([
                            'name'=>'cart','value'=>serialize($cart)
                        ]);
                        $cookies->add($cookie);
                        return ['status'=>1,'msg'=>'未登录修改商品数量成功'];
                    }
                }else{
                    //根据商品ID和登录用户ID查找对应购物车记录
                    $car=Cart::findOne(['goods_id'=>$goods_id,'member_id'=>\Yii::$app->user->id]);
                    if($amount==0){
                        $car->delete();
                        return ['status'=>1,'msg'=>'登录状态删除商品成功'];
                    }else{
                        $car->amount=$amount;
                        $car->save();
                        return ['status'=>1,'msg'=>'登录状态修改商品数量成功'];
                    }
                }
            }
            return ['status'=>-1,'msg'=>'购物车无对应商品'];
        }
        return ['status'=>-1,'msg'=>'请使用post请求'];
    }
    //清除购物车
    public function actionCleanCart(){
        //未登录状态下清除cookie，登录状态下清除数据库
        if(\Yii::$app->user->isGuest){
            //读取cookie里面的数据
            $cookies=\Yii::$app->request->cookies;
            $cookie=$cookies->get('cart');
            if($cookie!=null){
                $cookies=\Yii::$app->response->cookies;
                $cookies->remove('cart');
                return ['status'=>1,'msg'=>'清除cookie中的购物车数据成功'];
            }
            return ['status'=>-1,'msg'=>'cookie中无相关购物车数据'];
        }else{
            $carts=Cart::find()->where(['member_id'=>\Yii::$app->user->id])->all();
            if($carts){
                foreach($carts as $cart){
                    $cart->delete();
                }
                return ['status'=>1,'msg'=>'清除您的购物车数据成功'];
            }
            return ['status'=>-1,'msg'=>'购物车中没有您的相关数据'];
        }
    }
    //获取购物车所有商品
    public function actionCart(){
        if(\Yii::$app->user->isGuest){
            //读取cookie中的信息
            $cookies=\Yii::$app->request->cookies;
            $cookie=$cookies->get('cart');
            if($cookie!=null){
                $cart=unserialize($cookie->value);
                return ['status'=>1,'msg'=>'','data'=>$cart];
            }
            return ['status'=>-1,'msg'=>'cookie中无购物车数据'];
        }else{
            //读取当前用户的所有购物车数据
            $carts=Cart::find()->where(['member_id'=>\Yii::$app->user->id])->asArray()->all();
            if($carts){
                return ['status'=>1,'msg'=>'','data'=>$carts];
            }
            return ['status'=>-1,'msg'=>'您的购物车中没有相关数据'];
        }
    }
    //获取支付方式
    public function actionOrder()
    {
        $request = \Yii::$app->request;
        if (!\Yii::$app->user->isGuest) {
            if ($request->isPost) {
                $delivery_id = $request->post('delivery_id');
                $payment_id = $request->post('payment_id');
                $address_id = $request->post('address_id');
                $total_money = $request->post('total_money');
                //根据送货方式ID获取送货方式
                foreach (Order::$delivery_options as $deliverys) {
                    if ($delivery_id == $deliverys['id']) {
                        $delivery = $deliverys;
                    }
                }
                //根据支付方式ID查询支付方式
                foreach (Order::$payment_options as $payments) {
                    if ($payment_id ==$payments['id']) {
                        $payment = $payments;
                    }
                }
                //根据收货地址ID查询地址
                $address = Address::findOne(['id' => $address_id, 'member_id' => \Yii::$app->user->id]);
                $order = new Order();
                $order->member_id = \Yii::$app->user->id;
                $order->name = $address->name;
                $order->province = $address->province->name;
                $order->city = $address->city->name;
                $order->area = $address->area->name;
                $order->address = $address->detail_address;
                $order->tel = $address->phone;
                $order->delivery_id = $delivery['id'];
                $order->delivery_name = $delivery['name'];
                $order->delivery_price = $delivery['price'];
                $order->payment_id = $payment['id'];
                $order->payment_name = $payment['name'];
                $order->total = $total_money;
                if ($delivery['id'] == 1) {
                    $order->status = 2;
                } else {
                    $order->status = 1;
                }
                $order->create_time = time();
                //开启事务
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $order->save();
                    //保存订单商品表
                    //根据用户ID找到购物车的记录
                    $carts = Cart::find()->where(['member_id' => \Yii::$app->user->id])->all();
                    foreach ($carts as $cart) {
                        $order_goods = new OrderGoods();
                        $order_goods->order_id = $order->id;
                        $goods = Goods::findOne(['id' => $cart->goods_id]);
                        if ($goods == null) {
                            return ['status' => -1, 'msg' => '该商品已下架'];
                        }
                        if ($goods->stock < $cart->amount) {
                            return ['status' => -1, 'msg' => '该商品库存不足'];
                        }
                        $order_goods->goods_id = $cart->goods_id;
                        $order_goods->goods_name = $goods->name;
                        $order_goods->logo = $goods->logo;
                        $order_goods->price = $goods->shop_price;
                        $order_goods->amount = $cart->amount;
                        $order_goods->total = $cart->amount * $goods->shop_price;
                        $order_goods->save();
                        //商品库存对应减少
                        $goods->stock -= $cart->amount;
                        $goods->save();
                        //清除购物车记录
                        $cart->delete();
                    }
                    //提交
                    $transaction->commit();
                    return ['status' => 1, 'msg' => '提交订单成功','data'=>$order->id];
                } catch (Exception $e) {
                    $transaction->rollBack();
                    return ['status' => -1, 'msg' => '提交订单失败'];
                }

            }
            return ['status' => -1, 'msg' => '请用post提交'];
        }
        return  ['status'=>-1,'msg'=>'请先登录'];
    }
    //获取当前用户的订单列表
    public function actionOrderList(){
        if(!\Yii::$app->user->isGuest){
            $orders=Order::find()->where(['member_id'=>\Yii::$app->user->id])->asArray()->all();
            if($orders){
                return ['status'=>1,'msg'=>'','data'=>$orders];
            }
            return ['status'=>-1,'msg'=>'您还没有订单信息'];
        }
        return ['status'=>-1,'msg'=>'请先登录'];
    }
    //取消订单
    public function actionCancelOrder(){
        $request=\Yii::$app->request;
        if(!\Yii::$app->user->isGuest){
            $order_id=$request->get('order_id');
            $order=Order::findOne(['id'=>$order_id,'member_id'=>\Yii::$app->user->id]);
            if($order){
                $order->status=0;
                $order->save();
                //找到订单对应的商品
                foreach ($order->goods as $Order_goods){
                    $goods=Goods::findOne(['id'=>$Order_goods->goods_id]);
                    $goods->stock+=$Order_goods->amount;
                    $goods->save();
                }
                return ['status'=>1,'msg'=>'订单取消成功'];
            }
            return ['status'=>-1,'msg'=>'该订单不存在'];
        }
        return ['status'=>-1,'msg'=>'请先登录'];
    }



    //高级API
    //验证码
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'minLength'=>4,
                'maxLength'=>4,
            ],
        ];
    }
    //文件上传
    public function actionUpload()
    {
        $img = UploadedFile::getInstanceByName('img');
        if($img){
            $fileName = '/images/'.uniqid().'.'.$img->extension;
            $image = $img->saveAs(\Yii::getAlias('@webroot').$fileName,0);
            if($image){
                return ['status'=>'1','msg'=>'','data'=>$fileName];
            }
            return ['status'=>'-1','msg'=>$img->error];
        }
        return ['status'=>'-1','msg'=>'无文件上传'];
    }
    //手机验证码
    public function actionSendSms()
    {
        //确保上一次发送短信间隔超过1分钟
        $tel = \Yii::$app->request->post('tel');
        if(!preg_match('/^1[34578]\d{9}$/',$tel)){
            return ['status'=>'-1','msg'=>'电话号码不正确'];
        }
        //检查上次发送时间是否超过1分钟
        $value = \Yii::$app->cache->get('time_tel_'.$tel);
        $s = time()-$value;
        if($s <60){
            return ['status'=>'-1','msg'=>'请'.(60-$s).'秒后再试'];
        }

        $code = rand(100000,999999);
        $result = \Yii::$app->sms->setNum($tel)->setParam(['code' => $code])->send();
        //$result = 1;
        if($result){
            //保存当前验证码 session  mysql  redis  不能保存到cookie
//            \Yii::$app->session->set('code',$code);
//            \Yii::$app->session->set('tel_'.$tel,$code);
            \Yii::$app->cache->set('tel_'.$tel,$code,5*60);
            \Yii::$app->cache->set('time_tel_'.$tel,time(),5*60);
            return ['status'=>'1','msg'=>''];
        }else{
            return ['status'=>'-1','msg'=>'短信发送失败'];
        }
    }
    //分页在获取某商品分类或者品牌的商品里面
}