<?php
/**
 * @var $this \yii\web\View
 */
use yii\helpers\Html;
?>
<!-- 主体部分 start -->
<div class="mycart w990 mt10 bc">
    <h2><span>我的购物车</span></h2>
    <table>
        <thead>
        <tr>
            <th class="col1">商品名称</th>
            <th class="col3">单价</th>
            <th class="col4">数量</th>
            <th class="col5">小计</th>
            <th class="col6">操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($models as $model):?>
        <tr data-goods_id="<?=$model['id']?>" data-money="<?=$model['shop_price']*$model['number']?>" class="money" data-shop_price="<?=$model['shop_price']?>">
            <td class="col1"><a href=""><?=Html::img(Yii::$app->params['imageDomain'].$model['logo'])?></a>  <strong><a href=""><?=$model['name']?></a></strong></td>
            <td class="col3">￥<span><?=$model['shop_price']?></span></td>
            <td class="col4">
                <a href="javascript:;" class="reduce_num"></a>
                <input type="text" name="number" value="<?=$model['number']?>" class="amount"/>
                <a href="javascript:;" class="add_num"></a>
            </td>
            <td class="col5">￥<span><?=$model['shop_price']*$model['number']?></span></td>
            <td class="col6"><a href="javascript:;" class="del_goods">删除</a></td>
        </tr>
        <?php endforeach;?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="6">购物金额总计： <strong id="money">￥ <span id="total"></span></strong></td>
        </tr>
        </tfoot>
    </table>
    <div class="cart_btn w990 bc mt10">
        <a href="<?=\yii\helpers\Url::to(['member/index'])?>" class="continue">继续购物</a>
        <a href="<?php if(Yii::$app->user->isGuest){echo \yii\helpers\Url::to(['member/login']);}else{echo \yii\helpers\Url::to(['member/order']);}?>" class="checkout">结 算</a>
    </div>
</div>
<!-- 主体部分 end -->
<?php
/**
 * @var $this \yii\web\View
 */
$url=\yii\helpers\Url::to(['member/update-cart']);
//跨站请求
$token=Yii::$app->request->csrfToken;
$this->registerJs(new \yii\web\JsExpression(
        <<<JS
        //计算总金额
        $(function(){
            var count=0;
            $('.money').each(function(i,v){
                count+=parseInt($(v).attr('data-money'));
            });
            //将值赋给总金额
            $('#money').text(count);
        });
        //监听"+","-"按钮的点击事件
        $('.reduce_num,.add_num').click(function(){
            var goods_id=$(this).closest('tr').attr('data-goods_id');
            var number=$(this).closest('td').find('input').val();
            //更新总金额
            $(this).closest('tr').attr('data-money',number*$(this).closest('tr').attr('data-shop_price'));
            var count=0;
            $('.money').each(function(i,v){
                count+=parseInt($(v).attr('data-money'));
            });
            //将值赋给总金额
            $('#money').text(count);
            //发送AJAX请求
            $.post('$url',{goods_id:goods_id,number:number,'_csrf-frontend':"$token"});
        });
        //删除按钮
        $('.del_goods').click(function(){
            if(confirm('确认删除该商品')){
                var goods_id=$(this).closest('tr').attr('data-goods_id');
                //发送AJAX请求
                $.post('$url',{'goods_id':goods_id,'number':0,'_csrf-frontend':"$token"});
                //删除当前商品
                $(this).closest('tr').remove();
            }
        })
JS

));
