<?php

namespace frontend\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "member".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $email
 * @property string $tel
 * @property integer $last_login_time
 * @property integer $last_login_ip
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Member extends \yii\db\ActiveRecord implements IdentityInterface
{
    //密码明文
    public $password;
    //验证码
    public $code;
    //确认密码
    public $repassword;
    //自动登录
    public $cookie;
    public $messageCode;
    const SCENARIO_REGISTER='register';
    const SCENARIO_API_REGISTER = 'api_register';//api注册
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username','email','tel'],'required'],
            [['password','repassword','messageCode'],'required','on'=>self::SCENARIO_REGISTER],
            [['last_login_time', 'last_login_ip', 'status', 'created_at', 'updated_at'], 'integer'],
            [['username'], 'string', 'max' => 50],
            [['auth_key'], 'string', 'max' => 32],
            [['password_hash', 'email'], 'string', 'max' => 100],
            [['tel'], 'string', 'max' => 11],
            [['code'],'captcha','on'=>self::SCENARIO_REGISTER],
            ['code','captcha','on'=>self::SCENARIO_API_REGISTER,'captchaAction'=>'api/captcha'], //API验证码验证
            [['repassword'], 'compare','compareAttribute'=>'password','on'=>self::SCENARIO_REGISTER],
            [['cookie'],'safe'],
            //验证短信验证码
            ['messageCode','validateMessage','on'=>self::SCENARIO_REGISTER]
        ];
    }
    //验证短信验证码
    public function validateMessage(){
        //缓存里面没有该验证码
        $value = Yii::$app->cache->get('tel_'.$this->tel);
        if(!$value || $this->messageCode != $value){
            $this->addError('messageCode','验证码不正确');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'auth_key' => 'Auth Key',
            'password_hash' => '密码',
            'email' => '邮箱',
            'tel' => '电话',
            'last_login_time' => '最后登录',
            'last_login_ip' => '最后登录IP',
            'status' => '状态',
            'created_at' => '注册时间',
            'updated_at' => '更新时间',
            'password'=>'密码',
            'code'=>'验证码',
            'repassword'=>'确认密码 ',
            'cookie'=>'保存登录信息',
            'messageCode'=>'短信验证',
        ];
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        // TODO: Implement findIdentity() method.
        //通过ID获取用户
        return self::findOne(['id'=>$id]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        // TODO: Implement getId() method.
        //获取当前用户的ID
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
        return $this->getAuthKey()===$authKey;
    }
    public function generateAuthKey(){
        $this->auth_key=Yii::$app->security->generateRandomString();
    }
}
