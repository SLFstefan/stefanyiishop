<div>
    <ul class="breadcrumb">
        <li class="active">订单</li>
        <li class="active">Order</li>
    </ul>
</div>
<table class="table table-responsive table-bordered">
    <tr>
        <th>ID</th>
        <th>用户ID</th>
        <th>收货人</th>
        <th>详细地址</th>
        <th>电话</th>
        <th>配送方式</th>
        <th>支付方式</th>
        <th>订单金额</th>
        <th>订单状态</th>
        <th>创建时间</th>
        <th>操作</th>
    </tr>
    <?php foreach ($orders as $order):?>
        <tr>
            <td><?=$order->id?></td>
            <td><?=$order->member_id?></td>
            <td><?=$order->name?></td>
            <td><?=$order->address?></td>
            <td><?=$order->tel?></td>
            <td><?=$order->delivery_name?></td>
            <td><?=$order->payment_name?></td>
            <td><?='￥'.$order->total?></td>
            <td><?=\frontend\models\Order::$status_options[$order->status]?></td>
            <td><?=date('Y-m-d G:i:s',$order->create_time)?></td>
            <td>
                <?=\yii\bootstrap\Html::a('<span class="glyphicon glyphicon-gift"></span>发货',['order/send','id'=>$order->id],['class'=>'btn btn-primary btn-sm'])?>
                <?=\yii\bootstrap\Html::a('<span class="glyphicon glyphicon-trash"></span>删除',['order/del','id'=>$order->id],['class'=>'btn btn-danger btn-sm'])?>
            </td>
        </tr>
    <?php endforeach;?>
</table>
<?php
echo \yii\widgets\LinkPager::widget([
    'pagination'=>$page,
]);
