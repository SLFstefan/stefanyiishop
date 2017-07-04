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
<!-- 使用 -->
<div class="weui-cells__title">表单</div>
<form method="post">
    <div class="weui-cells weui-cells_form">
        <div class="weui-cell weui-cell_vcode">
            <div class="weui-cell__hd">
                <label class="weui-label">用户名</label>
            </div>
            <div class="weui-cell__bd">
                <input class="weui-input" name="username" type="text" placeholder="请输入用户名">
            </div>

        </div>

        <div class="weui-cell weui-cell_vcode">
            <div class="weui-cell__hd">
                <label class="weui-label">密码</label>
            </div>
            <div class="weui-cell__bd">
                <input class="weui-input" name="password" type="password" placeholder="请输入密码">
            </div>

        </div>
        <button  class="weui-btn weui-btn_primary">提交</button>
    </div>
</form>
<div class="weui-cells__tips">底部说明文字底部说明文字</div>
</body>
</html>

