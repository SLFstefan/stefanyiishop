<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>WeUI</title>
    <!-- 引入 WeUI -->
    <link rel="stylesheet" href="//res.wx.qq.com/open/libs/weui/1.1.2/weui.min.css"/>
</head>
<body>

<div class="weui-form-preview">
    <?php foreach ($orders as $order):?>
        <div class="weui-form-preview__hd">
            <label class="weui-form-preview__label">订单金额</label>
            <em class="weui-form-preview__value"><?=$order->total?></em>
        </div>
        <div class="weui-form-preview__bd">
            <p>
                <label class="weui-form-preview__label">收货地址</label>
                <span class="weui-form-preview__value"><?=$order->address?></span>
            </p>
            <p>
                <label class="weui-form-preview__label">收货人</label>
                <span class="weui-form-preview__value"><?=$order->name?></span>
            </p>
            <p>
                <label class="weui-form-preview__label">电话</label>
                <span class="weui-form-preview__value"><?=$order->tel?></span>
            </p>
            <p>
                <label class="weui-form-preview__label">送货方式</label>
                <span class="weui-form-preview__value"><?=$order->delivery_name?></span>
            </p>
            <p>
                <label class="weui-form-preview__label">支付方式</label>
                <span class="weui-form-preview__value"><?=$order->payment_name?></span>
            </p>
            <p>
                <label class="weui-form-preview__label">订单状态</label>
                <span class="weui-form-preview__value"><?=\frontend\models\Order::$status_options[$order->status]?></span>
            </p>
        </div>
        <div class="weui-form-preview__ft">
            <a class="weui-form-preview__btn weui-form-preview__btn_primary" href="javascript:">操作</a>
        </div>
    <?php endforeach;?>
</div>

<div class="weui-cells__tips">底部说明文字底部说明文字</div>
</body>
</html>