<?php
namespace frontend\assets;
use yii\web\AssetBundle;

class IndexAsset extends AssetBundle{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'style/base.css',
        'style/global.css',
        'style/header.css',
        'style/index.css',
        'style/bottomnav.css',
        'style/footer.css',
        'style/address.css',
        'style/home.css',
    ];
    public $js = [
        'js/header.js',
        'js/home.js',
        'js/index.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}