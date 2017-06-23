<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "address".
 *
 * @property integer $id
 * @property string $name
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $detail_address
 * @property string $phone
 * @property integer $status
 */
class Address extends \yii\db\ActiveRecord
{
    //获取省
    public function getProvince(){
        return $this->hasOne(Locations::className(),['id'=>'province_id']);
    }
    //获取市
    public function getCity(){
        return $this->hasOne(Locations::className(),['id'=>'city_id']);
    }
    //获取县区
    public function getArea(){
        return $this->hasOne(Locations::className(),['id'=>'area_id']);
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','phone','detail_address'],'required'],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['province_id', 'city_id','area_id'], 'integer'],
            [['detail_address'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 11],
            [['member_id'],'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '收货人',
            'province_id' => '',
            'city_id' => '',
            'area_id' => '',
            'detail_address' => '详细地址',
            'phone' => '电话',
            'status' => '默认',
        ];
    }
}
