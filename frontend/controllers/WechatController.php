<?php
namespace frontend\controllers;

use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\News;
use frontend\models\Address;
use frontend\models\Member;
use frontend\models\Order;
use yii\helpers\Url;
use yii\web\Controller;



class WechatController extends Controller{
    public $enableCsrfValidation=false;
    public function actionIndex(){
        $app = new Application(\Yii::$app->params['wechat']);
        $app->server->setMessageHandler(function ($message) {
            // $message->FromUserName // 用户的 openid
            // $message->MsgType // 消息类型：event, text....
            //return "您好！欢迎关注我!";
            switch ($message->MsgType){
                case 'text':
                    //文本消息
                    switch ($message->Content){
                        case '帮助':
                            return '你可以发送 优惠、解除绑定等信息';
                            break;
                        case '解除绑定':
                            //获取openID
                            $openid=$message->FromUserName;
                            $member=Member::findOne(['openid'=>$openid]);
                            $url=Url::to(['wechat/login'],true);
                            if($member!=null){
                                $member->openid='';
                                $member->save();
                                return '解绑成功';
                            }else{
                                return '你还没有绑定账户'.$url;
                            }
                            break;
                        case '优惠':
                            //多图文信息
                            $news1 = new News([
                                'title'       => '汉堡',
                                'description' => '德克士菠萝堡...',
                                'url'         => 'http://cc.sterose.xin/member/goods.html?goods_id=4',
                                'image'       => 'http://admin.yii2shop.com/images/593fb227e40ea.png',
                            ]);
                            $news2 = new News([
                                'title'       => '香奈儿',
                                'description' => '香奈儿香水...',
                                'url'         => 'http://cc.sterose.xin/member/goods.html?goods_id=1',
                                'image'       => 'http://admin.yii2shop.com/images/593e4d683de9d.png',
                            ]);
                            $news3 = new News([
                                'title'       => '辣条',
                                'description' => '辣辣辣辣条...',
                                'url'         => 'http://cc.sterose.xin/member/goods.html?goods_id=2',
                                'image'       => 'http://admin.yii2shop.com/images/593e546264487.png',
                            ]);
                            $news4 = new News([
                                'title'       => '绿豆糕',
                                'description' => '绿豆糕，青春的糕...',
                                'url'         => 'http://cc.sterose.xin/member/goods.html?goods_id=3',
                                'image'       => 'http://admin.yii2shop.com/images/593e78072c1ba.png',
                            ]);
                            $news5 = new News([
                                'title'       => '薯条',
                                'description' => '麦当劳薯条...',
                                'url'         => 'http://cc.sterose.xin/member/goods.html?goods_id=5',
                                'image'       => 'http://admin.yii2shop.com/images/594935256f7a5.png',
                            ]);
                            return [$news1,$news2,$news3,$news4,$news5];
                            break;
                    }



                    return '收到你的消息:'.$message->Content;
                    break;
                case 'event'://事件
                    //事件的类型   $message->Event
                    //事件的key值  $message->EventKey
                    if($message->Event == 'CLICK'){//菜单点击事件
                        if($message->EventKey == 'sale'){
                            $news1 = new News([
                                'title'       => '汉堡',
                                'description' => '德克士菠萝堡...',
                                'url'         => 'http://cc.sterose.xin/member/goods.html?goods_id=4',
                                'image'       => 'http://admin.yii2shop.com/images/593fb227e40ea.png',
                            ]);
                            $news2 = new News([
                                'title'       => '香奈儿',
                                'description' => '香奈儿香水...',
                                'url'         => 'http://cc.sterose.xin/member/goods.html?goods_id=1',
                                'image'       => 'http://admin.yii2shop.com/images/593e4d683de9d.png',
                            ]);
                            $news3 = new News([
                                'title'       => '辣条',
                                'description' => '辣辣辣辣条...',
                                'url'         => 'http://cc.sterose.xin/member/goods.html?goods_id=2',
                                'image'       => 'http://admin.yii2shop.com/images/593e546264487.png',
                            ]);
                            $news4 = new News([
                                'title'       => '绿豆糕',
                                'description' => '绿豆糕，青春的糕...',
                                'url'         => 'http://cc.sterose.xin/member/goods.html?goods_id=3',
                                'image'       => 'http://admin.yii2shop.com/images/593e78072c1ba.png',
                            ]);
                            $news5 = new News([
                                'title'       => '薯条',
                                'description' => '麦当劳薯条...',
                                'url'         => 'http://cc.sterose.xin/member/goods.html?goods_id=5',
                                'image'       => 'http://admin.yii2shop.com/images/594935256f7a5.png',
                            ]);
                            return [$news1,$news2,$news3,$news4,$news5];
                        }
                    }

                    return '接受到了'.$message->Event.'类型事件'.'key:'.$message->EventKey;
                    break;
            }
        });
        $response = $app->server->serve();
        // 将响应输出
        $response->send(); // Laravel 里请使用：return $response;
    }
    //设置菜单
    public function actionSetMenu(){
        $app = new Application(\Yii::$app->params['wechat']);
        $menu = $app->menu;
        $buttons = [
            [
                "type" => "click",
                "name" => "促销商品",
                "key"  => "sale"
            ],
            [
                "type" => "view",
                "name" => "在线商城",
                "url"  => Url::to(['member/index'],true),
            ],
            [
                "name"       => "个人中心",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "收货地址",
                        "url"  => Url::to(['wechat/address'],true)
                    ],
                    [
                        "type" => "view",
                        "name" => "修改密码",
                        "url"  => Url::to(['wechat/edit'],true)
                    ],
                    [
                        "type" => "view",
                        "name" => "我的订单",
                        "url"  => Url::to(['wechat/order'],true)
                    ],
                    [
                        "type" => "view",
                        "name" => "绑定账户",
                        "url" => Url::to(['wechat/login'],true)
                    ],
                ],
            ],
        ];
        $menu->add($buttons);
        //获取已设置的菜单（查询菜单）
        $menus = $menu->all();
        var_dump($menus);
    }
    //个人中心
    public function actionUser(){
        $openid = \Yii::$app->session->get('openid');
        if($openid == null){
            //获取用户的基本信息（openid），需要通过微信网页授权
            \Yii::$app->session->set('redirect',\Yii::$app->controller->action->uniqueId);
            //echo 'wechat-user';
            $app = new Application(\Yii::$app->params['wechat']);
            //发起网页授权
            $response = $app->oauth->scopes(['snsapi_userinfo'])
                ->redirect();
            $response->send();
        }
        var_dump($openid);
    }
    //授权回调页
    public function actionCallback(){
        $app = new Application(\Yii::$app->params['wechat']);
        $user = $app->oauth->user();
        //将openid放入session
        \Yii::$app->session->set('openid',$user->getId());
        return $this->redirect([\Yii::$app->session->get('redirect')]);
    }
    //我的订单
    public function actionOrder(){
        //openid
        $openid = \Yii::$app->session->get('openid');
        if($openid == null){
            //获取用户的基本信息（openid），需要通过微信网页授权
            \Yii::$app->session->set('redirect',\Yii::$app->controller->action->uniqueId);
            //echo 'wechat-user';
            $app = new Application(\Yii::$app->params['wechat']);
            //发起网页授权
            $response = $app->oauth->scopes(['snsapi_userinfo'])
                ->redirect();
            $response->send();
        }
        //var_dump($openid);
        //通过openid获取账号
        $member = Member::findOne(['openid'=>$openid]);
        if($member == null){
            //该openid没有绑定任何账户
            //引导用户绑定账户
            return $this->redirect(['wechat/login']);
        }else{
            //已绑定账户
            $orders = Order::findAll(['member_id'=>$member->id]);
            return $this->renderPartial('order',['orders'=>$orders]);
        }
    }
    //绑定用户账号   将openid和用户账号绑定
    public function actionLogin()
    {
        $openid = \Yii::$app->session->get('openid');
        if($openid == null){
            //获取用户的基本信息（openid），需要通过微信网页授权
            \Yii::$app->session->set('redirect',\Yii::$app->controller->action->uniqueId);
            //echo 'wechat-user';
            $app = new Application(\Yii::$app->params['wechat']);
            //发起网页授权
            $response = $app->oauth->scopes(['snsapi_userinfo'])
                ->redirect();
            $response->send();
        }

        //让用户登录，如果登录成功，将openid写入当前登录账户
        $request = \Yii::$app->request;
        if(\Yii::$app->request->isPost){
            $user = Member::findOne(['username'=>$request->post('username')]);
            if($user && \Yii::$app->security->validatePassword($request->post('password'),$user->password_hash)){
                \Yii::$app->user->login($user);
                //如果登录成功，将openid写入当前登录账户
                Member::updateAll(['openid'=>$openid],'id='.$user->id);
                if(\Yii::$app->session->get('redirect')) return $this->redirect([\Yii::$app->session->get('redirect')]);
                echo '绑定成功';exit;
            }else{
                echo '登录失败';exit;
            }
        }

        return $this->renderPartial('login');
    }
    //收货地址
    public function actionAddress(){
        //openid
        $openid = \Yii::$app->session->get('openid');
        if($openid == null){
            //获取用户的基本信息（openid），需要通过微信网页授权
            \Yii::$app->session->set('redirect',\Yii::$app->controller->action->uniqueId);
            //echo 'wechat-user';
            $app = new Application(\Yii::$app->params['wechat']);
            //发起网页授权
            $response = $app->oauth->scopes(['snsapi_userinfo'])
                ->redirect();
            $response->send();
        }
        //var_dump($openid);
        //通过openid获取账号
        $member = Member::findOne(['openid'=>$openid]);
        if($member == null){
            //该openid没有绑定任何账户
            //引导用户绑定账户
            return $this->redirect(['wechat/login']);
        }else{
            //已绑定账户
            $addresses = Address::findAll(['member_id'=>$member->id]);
            return $this->renderPartial('address',['addresses'=>$addresses]);
        }
    }
    //修改密码
    public function actionEdit(){
        //openid
        $openid = \Yii::$app->session->get('openid');
        if($openid == null){
            //获取用户的基本信息（openid），需要通过微信网页授权
            \Yii::$app->session->set('redirect',\Yii::$app->controller->action->uniqueId);
            //echo 'wechat-user';
            $app = new Application(\Yii::$app->params['wechat']);
            //发起网页授权
            $response = $app->oauth->scopes(['snsapi_userinfo'])
                ->redirect();
            $response->send();
        }
        //var_dump($openid);
        //通过openid获取账号
        $member = Member::findOne(['openid'=>$openid]);
        if($member == null){
            //该openid没有绑定任何账户
            //引导用户绑定账户
            return $this->redirect(['wechat/login']);
        }else{
            //已绑定账户
            //修改密码
            $request=\Yii::$app->request;
            if($request->isPost){
                if(\Yii::$app->security->validatePassword($request->post('old_password'),$member->password_hash)){
                    if($request->post('new_password')==$request->post('repassword')){
                        $member->password_hash=\Yii::$app->security->generatePasswordHash($request->post('new_password'));
                        $member->save();
                        echo '密码修改成功';exit;
                    }else{
                        echo '两次输入的密码不一致';exit;
                    }
                }else{
                    echo '旧密码错误';exit;
                }
            }
            return $this->renderPartial('edit');
        }
    }
}