<?php
use yii\helpers\Html;
?>
<!-- 登录主体部分start -->
<div class="login w990 bc mt10 regist">
    <div class="login_hd">
        <h2>用户注册</h2>
        <b></b>
    </div>
    <div class="login_bd">
        <div class="login_form fl">
            <?php
            $form=\yii\widgets\ActiveForm::begin(
                    ['fieldConfig'=>[
                            'options'=>[
                                    'tag'=>'li'
                            ],
                            'errorOptions'=>[
                                    'tag'=>'p'
                            ]
                    ]]
            );
            echo '<ul>';
            echo $form->field($model,'username')->textInput(['class'=>'txt']);
            echo $form->field($model,'password')->passwordInput(['class'=>'txt']);
            echo $form->field($model,'repassword')->passwordInput(['class'=>'txt']);
            echo $form->field($model,'email')->textInput(['class'=>'txt']);
            echo $form->field($model,'tel')->textInput(['class'=>'txt']);
            //短信验证
            $button =  Html::button('发送短信验证码',['id'=>'send_sms_button']);
            echo $form->field($model,'messageCode',['options'=>['class'=>'checkcode'],'template'=>"{label}\n{input}$button\n{hint}\n{error}"])->textInput(['class'=>'txt']);
            //验证码
            echo $form->field($model,'code',['options'=>['class'=>'checkcode']])->widget(\yii\captcha\Captcha::className(),['template'=>'{input}{image}']);
            echo '<li><label>&nbsp;</label></label><input type="checkbox" class="chb" checked="checked" /> 我已阅读并同意《用户注册协议》</li>';
            //echo \yii\helpers\Html::submitButton('注册');
            echo '<li><label for="">&nbsp;</label><input type="submit" value="" class="login_btn"></li>';
            echo '</ul>';
            \yii\widgets\ActiveForm::end();
            ?>
            <!--<li>
                <label for="">验证码：</label>
                <input type="text" class="txt" value="" placeholder="请输入短信验证码" name="captcha" disabled="disabled" id="captcha"/> <input type="button" onclick="bindPhoneNum(this)" id="get_captcha" value="获取验证码" style="height: 25px;padding:3px 8px"/>

            </li>-->
        </div>

        <div class="mobile fl">
            <h3>手机快速注册</h3>
            <p>中国大陆手机用户，编辑短信 “<strong>XX</strong>”发送到：</p>
            <p><strong>1069099988</strong></p>
        </div>

    </div>
</div>
<!-- 登录主体部分end -->
<?php
$url = \yii\helpers\Url::to(['member/send-sms']);
$this->registerJs(new \yii\web\JsExpression(
    <<<JS
    $("#send_sms_button").click(function(){
        //发送验证码按钮被点击时
        //手机号
        var tel = $("#member-tel").val();
        //AJAX post提交tel参数到 member/send-sms
        $.post('$url',{tel:tel},function(data){
            if(data == 'success'){
                console.log('短信发送成功');
                alert('短信发送成功');
            }else{
                console.log(data);
            }
        });
    });
JS
));
