<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username');
echo $form->field($model,'password')->passwordInput();
//自动登录
echo $form->field($model,'cookie')->checkbox();
//验证码
echo $form->field($model,'code')->widget(\yii\captcha\Captcha::className(),[
    'captchaAction'=>'user/captcha',
    'template'=>'<div class="row"><div class="col-md-1">{image}</div><div class="col-md-2">{input}</div></div>']);
echo \yii\bootstrap\Html::submitButton('登录',['class'=>'btn btn-primary']);
\yii\bootstrap\ActiveForm::end();