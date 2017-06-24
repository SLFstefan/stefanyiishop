<!-- 导航条部分 start -->
<div class="nav w1210 bc mt10">
    <!--  商品分类部分 start-->
    <div class="category fl cat1"> <!-- 非首页，需要添加cat1类 -->
        <div class="cat_hd">  <!-- 注意，首页在此div上只需要添加cat_hd类，非首页，默认收缩分类时添加上off类，鼠标滑过时展开菜单则将off类换成on类 -->
            <h2>全部商品分类</h2>
            <em></em>
        </div>

        <div class="cat_bd none">
            <?=\frontend\widgets\CategoryWidget::widget()?>
        </div>

    </div>
    <!--  商品分类部分 end-->

    <div class="navitems fl">
        <ul class="fl">
            <li class="current"><a href="">首页</a></li>
            <li><a href="">电脑频道</a></li>
            <li><a href="">家用电器</a></li>
            <li><a href="">品牌大全</a></li>
            <li><a href="">团购</a></li>
            <li><a href="">积分商城</a></li>
            <li><a href="">夺宝奇兵</a></li>
        </ul>
        <div class="right_corner fl"></div>
    </div>
</div>
<!-- 导航条部分 end -->
</div>
<!-- 头部 end-->

<div style="clear:both;"></div>
<!-- 页面主体 start -->
<div class="main w1210 bc mt10">
    <div class="crumb w1210">
        <h2><strong>我的XX </strong><span>> 我的订单</span></h2>
    </div>

    <!-- 左侧导航菜单 start -->
    <div class="menu fl">
        <h3>我的XX</h3>
        <div class="menu_wrap">
            <dl>
                <dt>订单中心 <b></b></dt>
                <dd><b>.</b><a href="">我的订单</a></dd>
                <dd><b>.</b><a href="">我的关注</a></dd>
                <dd><b>.</b><a href="">浏览历史</a></dd>
                <dd><b>.</b><a href="">我的团购</a></dd>
            </dl>

            <dl>
                <dt>账户中心 <b></b></dt>
                <dd class="cur"><b>.</b><a href="">账户信息</a></dd>
                <dd><b>.</b><a href="">账户余额</a></dd>
                <dd><b>.</b><a href="">消费记录</a></dd>
                <dd><b>.</b><a href="">我的积分</a></dd>
                <dd><b>.</b><a href="">收货地址</a></dd>
            </dl>

            <dl>
                <dt>订单中心 <b></b></dt>
                <dd><b>.</b><a href="">返修/退换货</a></dd>
                <dd><b>.</b><a href="">取消订单记录</a></dd>
                <dd><b>.</b><a href="">我的投诉</a></dd>
            </dl>
        </div>
    </div>
    <!-- 左侧导航菜单 end -->

    <!-- 右侧内容区域 start -->
    <div class="content fl ml10">
        <div class="address_hd">
            <h3>收货地址薄</h3>
            <?php foreach ($addresses as $address):?>
            <dl>
                <dt><?=$address->id.'、'.$address->name.'—'.$address->province->name.'—'.$address->city->name.'—'.$address->area->name.'—'.$address->detail_address.'—'.$address->phone?></dt>
                <dd>
                    <a href="update-address.html?id=<?=$address->id?>">修改</a>
                    <a href="del-address.html?id=<?=$address->id?>">删除</a>
                    <a href="default-address.html?id=<?=$address->id?>">设为默认地址</a>
                </dd>
            </dl>
            <?php endforeach;?>
        </div>

        <div class="address_bd mt10">
            <h4>新增收货地址</h4>
            <?php
            $form=\yii\widgets\ActiveForm::begin(
                ['fieldConfig'=>[
                    'options'=>[
                        'tag'=>'li'
                    ],
                    'errorOptions'=>[
                        'tag'=>'a'
                    ]
                ]]
            );
            echo '<ul>';
            echo $form->field($model,'name')->textInput(['class'=>'txt']);
            echo $form->field($model,'province_id')->hiddenInput();
            echo $form->field($model,'city_id')->hiddenInput();
            echo $form->field($model,'area_id')->hiddenInput();
            echo '所在地区：<select name="province"><option>请选择省份</option></select><select name="city"><option>请选择城市</option></select><select name="area"><option>请选择区县</option></select>';
            $province_id = $model->province_id?$model->province_id:0;
            $city_id=$model->city_id?$model->city_id:0;
            $area_id=$model->area_id?$model->area_id:0;
            $js=new \yii\web\JsExpression(
                    <<<JS
                //因为我们首先要选择省份，所以我们要准备省份数据
			$(function(){
				//因为查看省份的时候我们需要parent_id,所以我们要传一个parent_id给PHP
				var data={
					'id':0,
				};
				//因为我们要通过PHP从数据库读取数据，所以我们通过AJAX发送请求，从数据库读取数据
				$.getJSON('read.html',data,function(response){
					//因为PHP返回了多条数据，所以我们需要遍历response
					$(response).each(function(i,v){
                        if( v.id ==$province_id){
                            var html='<option value="'+v.id+'" selected>'+v.name+'</option>';  
					    }else{
					        //因为插入的文本可能会很长，所以我们将文本放入到一个变量中方便使用
						    var html='<option value="'+v.id+'">'+v.name+'</option>';
					    }				
						//因为我们需要将数据放入select下拉框中，所以我们通过HTML将数据放入到下拉框中
						$(html).appendTo('select[name=province]');
					   
					});
					 $('select[name=province]').trigger("change");
				});
			});
			//因为我们选择省份过后要选择市，所以我们要准备市数据，当省份的下拉框触发change事件时，我们视为选择了省份
			$('select[name=province]').on('change',function(){
				//因为重新选择省的时候，上一次所选择的省的市和区县没有清空，所以我们要清空上一次的市和区县
				$('select[name=city]').find('option:not(:first)').remove();
				//清空区县
				$('select[name=area]').find('option:not(:first)').remove();
				//因为选择市的时候我们需要省份的ID，所以我们需要传一个ID给php
				var data={
					'id':$(this).val(),
				};
				//获取省ID
				$('#address-province_id').val($('select[name=province]').val());
				//因为当我们选择请选择的时候，之前的选择没有赋空，所以我们要做一个判断
				if($(this).val()==='请选择省份'){return false;}
				//因为我们要通过PHP从数据库读取数据，所以我们通过AJAX发送请求，从数据库读取数据
				$.getJSON('read.html',data,function(response){
					//因为PHP返回了多条数据，所以我们需要遍历response
					$(response).each(function(i,v){
                            if( v.id==$city_id){
                                var html='<option value="'+v.id+'" selected>'+v.name+'</option>';
					        }else{
					            //因为插入的文本可能会很长，所以我们将文本放入到一个变量中方便使用
                                var html='<option value="'+v.id+'">'+v.name+'</option>';
					        }
						//因为我们需要将数据放入select下拉框中，所以我们通过HTML将数据放入到下拉框中
						$(html).appendTo('select[name=city]');
					});
					$('select[name=city]').trigger("change");
				});
			});
			//回显省份
			//因为我们选择市过后要选择市，所以我们要准备区县数据，当市的下拉框触发change事件时，我们视为选择了市
			$('select[name=city]').on('change',function(){
				//因为重新选择市的时候，上一次所选择的市区县没有清空，所以我们要清空上一次的区县
				$('select[name=area]').find('option:not(:first)').remove();
				//因为选择区县的时候我们需要市的ID，所以我们需要传一个ID给php
				var data={
					'id':$(this).val(),
				};
				//获取市ID
				$('#address-city_id').val($('select[name=city]').val());
				//因为当我们选择请选择的时候，之前的选择没有赋空，所以我们要做一个判断
				if($(this).val()==='请选择城市'){return false;}
				//因为我们要通过PHP从数据库读取数据，所以我们通过AJAX发送请求，从数据库读取数据
				$.getJSON('read.html',data,function(response){
					//因为PHP返回了多条数据，所以我们需要遍历response
					$(response).each(function(i,v){
                        if(v.id == $area_id){
                            var html='<option value="'+v.id+'" selected>'+v.name+'</option>';
					    }else{
					        //因为插入的文本可能会很长，所以我们将文本放入到一个变量中方便使用
						    var html='<option value="'+v.id+'">'+v.name+'</option>';
					    }
						//因为我们需要将数据放入select下拉框中，所以我们通过HTML将数据放入到下拉框中
						$(html).appendTo('select[name=area]');
					});
				});
			});
			$('select[name=area]').on('change',function(){
			    //获取区县ID
			    $('#address-area_id').val($('select[name=area]').val());
			});
JS
            );
            //加载js
            $this->registerJs($js);
            echo $form->field($model,'detail_address')->textInput(['class'=>'txt']);
            echo $form->field($model,'phone')->textInput(['class'=>'txt']);
            echo $form->field($model,'status')->checkbox();
            echo '<li><label for="">&nbsp;</label><input type="submit" name="" class="btn" value="保存" /></li>';
            echo '</ul>';
            \yii\widgets\ActiveForm::end();
            ?>
        </div>

    </div>
    <!-- 右侧内容区域 end -->
</div>
<!-- 页面主体 end-->