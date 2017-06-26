<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $name
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $address
 * @property string $tel
 * @property integer $delivery_id
 * @property string $delivery_name
 * @property string $delivery_price
 * @property integer $payment_id
 * @property string $payment_name
 * @property string $total
 * @property integer $status
 * @property integer $trade_no
 * @property integer $create_time
 */
class Order extends \yii\db\ActiveRecord
{
    public static $delivery_options=[
        ['id'=>1,'name'=>'普通快递送货上门','price'=>'10'],
        ['id'=>2,'name'=>'特快专递','price'=>'40'],
        ['id'=>3,'name'=>'加急快递送货上门','price'=>'40'],
        ['id'=>4,'name'=>'平邮','price'=>'10'],
    ];
    public static $payment_options=[
        ['id'=>1,'name'=>'货到付款','description'=>'送货上门后再收款，支持现金、POS机刷卡、支票支付'],
        ['id'=>2,'name'=>'在线支付','description'=>'即时到帐，支持绝大数银行借记卡及部分银行信用卡'],
        ['id'=>3,'name'=>'上门自提','description'=>'自提时付款，支持现金、POS刷卡、支票支付'],
        ['id'=>4,'name'=>'邮局汇款','description'=>'通过快钱平台收款 汇款后1-3个工作日到账'],
    ];
    public $address_id;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'delivery_id', 'payment_id', 'status', 'trade_no', 'create_time'], 'integer'],
            [['total'], 'number'],
            [['name'], 'string', 'max' => 50],
            [['province', 'city', 'area'], 'string', 'max' => 20],
            [['address', 'delivery_name', 'delivery_price', 'payment_name'], 'string', 'max' => 255],
            [['tel'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '用户ID',
            'name' => '收货人',
            'province' => '省',
            'city' => '市',
            'area' => '县',
            'address' => '详细地址',
            'tel' => '电话号码',
            'delivery_id' => '配送方式ID',
            'delivery_name' => '配送方式名称',
            'delivery_price' => '配送方式价格',
            'payment_id' => '支付方式ID',
            'payment_name' => '支付方式名称',
            'total' => '总金额',
            'status' => '订单状态',
            'trade_no' => '第三方交易号',
            'create_time' => '创建时间',
            'address_id'=>'送货方式',
        ];
    }
}
