<?php

namespace frontend\controllers;

use backend\models\Goods;
use backend\models\GoodsCategory;
use frontend\models\Address;
use frontend\models\Locations;
use frontend\models\LoginForm;
use frontend\models\Member;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

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

}
