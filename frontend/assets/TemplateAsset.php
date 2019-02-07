<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * assets для внутренних страниц приложения
 * Class TemplateAsset
 * @package app\assets
 */
class TemplateAsset extends AssetBundle {

    public $sourcePath = '@webroot/frontend/web';
    public $basePath = '@webroot/frontend/web';
    public $baseUrl = '@webroot/frontend/web';
    public $js = [
        "/vendor/jquery/jquery.min.js",
        "/vendor/bootstrap/js/bootstrap.bundle.min.js",
        "/vendor/jquery-easing/jquery.easing.min.js",
        "/vendor/magnific-popup/jquery.magnific-popup.min.js",
        "/js/jqBootstrapValidation.js",
        "/js/contact_me.js",
        "/js/freelancer.min.js",
    ];
    public $css = [
        "/vendor/fontawesome-free/css/all.min.css",
        "/vendor/magnific-popup/magnific-popup.css",
        "/css/freelancer.min.css",
    ];
    public $depends = [
        'yii\bootstrap4\BootstrapAsset'
    ];
    public $jsOptions = [
            //'position' => \yii\web\View::POS_HEAD
    ];

}
