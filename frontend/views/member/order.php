<?php
/**
 * @var $this \yii\web\View
 */
?>
<!-- 主体部分 start -->
<div class="fillin w990 bc mt15">
    <div class="fillin_hd">
        <h2>填写并核对订单信息</h2>
    </div>
    <div class="fillin_bd">
        <!-- 收货人信息  start-->
        <div class="address">
            <h3>收货人信息</h3>
            <div class="address_info">
                <?php foreach ($addresses as $address):?>
                <p><input type="radio" value="<?=$address->id?>" name="order[address_id]"/><?=$address->name.'&nbsp'.$address->phone.'&nbsp'.$address->detail_address?></p>
                <?php endforeach;?>
            </div>
        </div>
        <!-- 收货人信息  end-->

        <!-- 配送方式 start -->
        <div class="delivery">
            <h3>送货方式 </h3>


            <div class="delivery_select">
                <table>
                    <thead>
                    <tr>
                        <th class="col1">送货方式</th>
                        <th class="col2">运费</th>
                        <th class="col3">运费标准</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach (\frontend\models\Order::$delivery_options as $key=>$delivery):?>
                    <tr <?=$key==0?'class="cur"':''?>>
                        <td>
                            <input type="radio" name="delivery" <?=$key==0?"checked":''?>  value="<?=$delivery['id']?>"/><?=$delivery['name']?>

                        </td>
                        <td>￥<?=$delivery['price']?></td>
                        <td>每张订单不满499.00元,运费15.00元, 订单4...</td>
                    </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>

            </div>
        </div>
        <!-- 配送方式 end -->

        <!-- 支付方式  start-->
        <div class="pay">
            <h3>支付方式 </h3>


            <div class="pay_select">
                <table>
                    <?php foreach (\frontend\models\Order::$payment_options as $key=>$payment):?>
                    <tr class="payment_id">
                        <td class="col1"><input type="radio" name="order[payment_id]" value="<?=$payment['id']?>"/><?=$payment['name']?></td>
                        <td class="col2"><?=$payment['description']?></td>
                    </tr>
                    <?php endforeach;?>
                </table>

            </div>
        </div>
        <!-- 支付方式  end-->

        <!-- 发票信息 start-->
        <div class="receipt none">
            <h3>发票信息 </h3>


            <div class="receipt_select ">
                <form action="">
                    <ul>
                        <li>
                            <label for="">发票抬头：</label>
                            <input type="radio" name="type" checked="checked" class="personal" />个人
                            <input type="radio" name="type" class="company"/>单位
                            <input type="text" class="txt company_input" disabled="disabled" />
                        </li>
                        <li>
                            <label for="">发票内容：</label>
                            <input type="radio" name="content" checked="checked" />明细
                            <input type="radio" name="content" />办公用品
                            <input type="radio" name="content" />体育休闲
                            <input type="radio" name="content" />耗材
                        </li>
                    </ul>
                </form>

            </div>
        </div>
        <!-- 发票信息 end-->

        <!-- 商品清单 start -->
        <div class="goods">
            <h3>商品清单</h3>
            <table>
                <thead>
                <tr>
                    <th class="col1">商品</th>
                    <th class="col3">价格</th>
                    <th class="col4">数量</th>
                    <th class="col5">小计</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($carts as $cart):?>
                <tr data-goods_id="<?=$cart->goods_id?>" data-price="<?=$cart->goods->shop_price?>" class="count" data-count="<?=$cart->goods->shop_price*$cart->amount?>">
                    <td class="col1"><a href=""><img src="<?=Yii::$app->params['imageDomain'].$cart->goods->logo?>" alt="" /></a>  <strong><a href=""><?=$cart->goods->name?></a></strong></td>
                    <td class="col3">￥<?=$cart->goods->shop_price?></td>
                    <td class="col4"> <?=$cart->amount?></td>
                    <td class="col5"><span><?=$cart->goods->shop_price*$cart->amount?></span></td>
                </tr>
                <?php endforeach;?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="5">
                        <ul>
                            <li>
                                <span id="count"><span></span>件商品，总商品金额￥：</span>
                                <em id="money"></em>
                            </li>
                            <li>
                                <span>返现-￥：</span>
                                <em>240.00</em>
                            </li>
                            <li>
                                <span>运费￥：</span>
                                <em id="delivery">10.00</em>
                            </li>
                            <li>
                                <span>应付总额￥：</span>
                                <em class="total_money"></em>
                            </li>
                        </ul>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
        <!-- 商品清单 end -->

    </div>

    <div class="fillin_ft">
        <a href="<?=\yii\helpers\Url::to(['member/success'])?>"><span>提交订单</span></a>
        <p>应付总额￥：<strong class="total_money"></strong></p>

    </div>
</div>
<!-- 主体部分 end -->
<?php
$url=\yii\helpers\Url::to(['member/add-order']);
$token=Yii::$app->request->csrfToken;
$this->registerJs(new \yii\web\JsExpression(
        <<<JS
        var delivery_id=1;
        var payment_id=0;
        var address_id=0;
        var total_money=0;
        $(function(){
            var count=0;
            var money=0;
           $('.count').each(function(i,v){
               count+=1;
               money+=parseInt($(v).attr('data-count'));
           }) 
           $('#count').find('span').text(count);
           $('#money').text(money);
           //获取应付总额
           $('.total_money').text(money+parseInt($('.cur').find('td:eq(1)').text().substring(1,$('.cur').find('td:eq(1)').text().length)));
        });
        //送货方式改变，改变运费
        $('input[name=delivery]').change(function(){
            $('#delivery').text('￥'+parseInt($('.cur').find('td:eq(1)').text().substring(1,$('.cur').find('td:eq(1)').text().length)));
            //获取应付总额
           $('.total_money').text( parseInt($('#money').text())+parseInt($('.cur').find('td:eq(1)').text().substring(1,$('.cur').find('td:eq(1)').text().length)));
        });
        //绑定提交订单的click事件
        $('.fillin_ft').find('a').click(function(){
            //获取支付方式，送货方式，地址ID
            delivery_id=$('.cur').find('input').val();
            address_id=$('.address_info').find('input:checked').val();
            payment_id=$('.pay_select').find('input:checked').val();
            total_money=parseInt($('.fillin_ft').find('strong').text());
            //发送AJAX请求
            $.post("$url",{'delivery_id':delivery_id,'address_id':address_id,'payment_id':payment_id,'total_money':total_money,'_csrf-frontend':"$token"},function(){});
        })
       
        
JS

));
