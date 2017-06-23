<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

\frontend\assets\GoodsAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <title>京西商城</title>
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
    <?php $this->beginBody() ?>

    <!-- 顶部导航 start -->
    <div class="topnav">
        <div class="topnav_bd w1210 bc">
            <div class="topnav_left">

            </div>
            <div class="topnav_right fr">
                <ul>
                    <?php if(Yii::$app->user->isGuest){?>
                        <li>您好，欢迎来到京西！[<a href="login.html">登录</a>] [<a href="register.html">免费注册</a>] </li>
                    <?php }else{
                        echo '<li class="prompt">京西会员：'.Yii::$app->user->identity->username;
                        echo '[<a href="logout.html">注销登录</a>]</li>';
                    }?>
                    <li class="line">|</li>
                    <li>我的订单</li>
                    <li class="line">|</li>
                    <li>客户服务</li>

                </ul>
            </div>
        </div>
    </div>
    <!-- 顶部导航 end -->

    <div style="clear:both;"></div>

    <!-- 头部 start -->
    <div class="header w1210 bc mt15">
        <!-- 头部上半部分 start 包括 logo、搜索、用户中心和购物车结算 -->
        <div class="logo w1210">
            <h1 class="fl"><a href="index.html"><?=Html::img('@web/images/logo.png',['alt'=>'京西商城'])?></a></h1>
            <!-- 头部搜索 start -->
            <div class="search fl">
                <div class="search_form">
                    <div class="form_left fl"></div>
                    <form action="" name="serarch" method="get" class="fl">
                        <input type="text" class="txt" value="请输入商品关键字" /><input type="submit" class="btn" value="搜索" />
                    </form>
                    <div class="form_right fl"></div>
                </div>

                <div style="clear:both;"></div>

                <div class="hot_search">
                    <strong>热门搜索:</strong>
                    <a href="">D-Link无线路由</a>
                    <a href="">休闲男鞋</a>
                    <a href="">TCL空调</a>
                    <a href="">耐克篮球鞋</a>
                </div>
            </div>
            <!-- 头部搜索 end -->

            <!-- 用户中心 start-->
            <div class="user fl">
                <dl>
                    <dt>
                        <em></em>
                        <a href="">用户中心</a>
                        <b></b>
                    </dt>
                    <dd>
                        <?php if(Yii::$app->user->isGuest){?>
                            <div class="prompt">
                                您好，请<a href="login.html">登录</a>
                            </div>
                        <?php }else{
                            echo '<div class="prompt">'.Yii::$app->user->identity->username;
                            echo '[<a href="logout.html">注销登录</a>]</div>';
                        }?>

                        <div class="uclist mt10">
                            <ul class="list1 fl">
                                <li><a href="">用户信息></a></li>
                                <li><a href="">我的订单></a></li>
                                <li><a href="address.html">收货地址></a></li>
                                <li><a href="">我的收藏></a></li>
                            </ul>

                            <ul class="fl">
                                <li><a href="">我的留言></a></li>
                                <li><a href="">我的红包></a></li>
                                <li><a href="">我的评论></a></li>
                                <li><a href="">资金管理></a></li>
                            </ul>

                        </div>
                        <div style="clear:both;"></div>
                        <div class="viewlist mt10">
                            <h3>最近浏览的商品：</h3>
                            <ul>
                                <li><a href=""><?=Html::img('@web/images/view_list1.jpg')?></a></li>
                                <li><a href=""><?=Html::img('@web/images/view_list2.jpg')?></a></li>
                                <li><a href=""><?=Html::img('@web/images/view_list3.jpg')?></a></li>
                            </ul>
                        </div>
                    </dd>
                </dl>
            </div>
            <!-- 用户中心 end-->

            <!-- 购物车 start -->
            <div class="cart fl">
                <dl>
                    <dt>
                        <a href="">去购物车结算</a>
                        <b></b>
                    </dt>
                    <dd>
                        <div class="prompt">
                            购物车中还没有商品，赶紧选购吧！
                        </div>
                    </dd>
                </dl>
            </div>
            <!-- 购物车 end -->
        </div>
        <!-- 头部上半部分 end -->

        <div style="clear:both;"></div>


        <?=$content?>

        <!--1F 电脑办公 start -->


        <div style="clear:both;"></div>

        <!-- 底部导航 start -->
        <div class="bottomnav w1210 bc mt10">
            <div class="bnav1">
                <h3><b></b> <em>购物指南</em></h3>
                <ul>
                    <li><a href="">购物流程</a></li>
                    <li><a href="">会员介绍</a></li>
                    <li><a href="">团购/机票/充值/点卡</a></li>
                    <li><a href="">常见问题</a></li>
                    <li><a href="">大家电</a></li>
                    <li><a href="">联系客服</a></li>
                </ul>
            </div>

            <div class="bnav2">
                <h3><b></b> <em>配送方式</em></h3>
                <ul>
                    <li><a href="">上门自提</a></li>
                    <li><a href="">快速运输</a></li>
                    <li><a href="">特快专递（EMS）</a></li>
                    <li><a href="">如何送礼</a></li>
                    <li><a href="">海外购物</a></li>
                </ul>
            </div>


            <div class="bnav3">
                <h3><b></b> <em>支付方式</em></h3>
                <ul>
                    <li><a href="">货到付款</a></li>
                    <li><a href="">在线支付</a></li>
                    <li><a href="">分期付款</a></li>
                    <li><a href="">邮局汇款</a></li>
                    <li><a href="">公司转账</a></li>
                </ul>
            </div>

            <div class="bnav4">
                <h3><b></b> <em>售后服务</em></h3>
                <ul>
                    <li><a href="">退换货政策</a></li>
                    <li><a href="">退换货流程</a></li>
                    <li><a href="">价格保护</a></li>
                    <li><a href="">退款说明</a></li>
                    <li><a href="">返修/退换货</a></li>
                    <li><a href="">退款申请</a></li>
                </ul>
            </div>

            <div class="bnav5">
                <h3><b></b> <em>特色服务</em></h3>
                <ul>
                    <li><a href="">夺宝岛</a></li>
                    <li><a href="">DIY装机</a></li>
                    <li><a href="">延保服务</a></li>
                    <li><a href="">家电下乡</a></li>
                    <li><a href="">京东礼品卡</a></li>
                    <li><a href="">能效补贴</a></li>
                </ul>
            </div>
        </div>
        <!-- 底部导航 end -->

        <div style="clear:both;"></div>
        <!-- 底部版权 start -->
        <div class="footer w1210 bc mt10">
            <p class="links">
                <a href="">关于我们</a> |
                <a href="">联系我们</a> |
                <a href="">人才招聘</a> |
                <a href="">商家入驻</a> |
                <a href="">千寻网</a> |
                <a href="">奢侈品网</a> |
                <a href="">广告服务</a> |
                <a href="">移动终端</a> |
                <a href="">友情链接</a> |
                <a href="">销售联盟</a> |
                <a href="">京西论坛</a>
            </p>
            <p class="copyright">
                © 2005-2013 京东网上商城 版权所有，并保留所有权利。  ICP备案证书号:京ICP证070359号
            </p>
            <p class="auth">
                <a href=""><?=Html::img('@web/images/xin.png')?></a>
                <a href=""><?=Html::img('@web/images/kexin.jpg')?></a>
                <a href=""><?=Html::img('@web/images/police.jpg')?></a>
                <a href=""><?=Html::img('@web/images/beian.gif')?></a>
            </p>
        </div>
        <!-- 底部版权 end -->

        <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>