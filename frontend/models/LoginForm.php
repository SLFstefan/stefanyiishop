<?php
namespace frontend\models;
use yii\base\Model;

class LoginForm extends Model{
    public $username;
    public $password;
    public $cookie;
    public $code;
    //重写验证方法
    public function rules()
    {
        return [
            [['username','password'],'required'],
            [['username'],'nameValidate'],
            [['cookie'],'safe'],
            [['code'],'captcha'],
        ];
    }
    //标签名
    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'password'=>'密码',
            'code'=>'验证码',
            'cookie'=>'',
        ];
    }
    //自定义验证方法
    public function nameValidate(){
        //查询数据库
        $user=Member::findOne(['username'=>$this->username]);
        //验证用户名
        if($user){
            //验证密码
            if(!\Yii::$app->security->validatePassword($this->password,$user->password_hash)){
                $this->addError('username','用户名或密码错误');
            }else{
                //保存随机cookie字符串
                $user->generateAuthKey();
                $user->save(false);
                //设置生命周期，在main里面配置authTimeout
                $cookie=\Yii::$app->user->authTimeout;
                //验证成功，登录
                \Yii::$app->user->login($user,$this->cookie?$cookie:0);
            }
        }else{
            //验证失败
            $this->addError('username','用户名或密码错误');
        }
    }
}