<?php

namespace appitnetwork\wpthemes\assets;

use Yii;
use yii\web\AssetBundle;
use yii\helpers\FileHelper;

class WP_AdminAsset extends AssetBundle
{
    public $sourcePath = '@vendor/appitnetwork/yii2-wordpress-themes/src/wordpress/wp-admin';
    public $css = [];
    public $js = [];
    public $depends = [];
    public $publishOptions = [
        'only' => [
            '*.js',
            '*.css',
            '*.eot',
            '*.ttf',
            '*.svg',
            '*.woff',
            '*.less',
            '*.scss',
            '*.styl',
            '*.coffee',
            '*.ts',
            '*.png',
            '*.gif',
            '*.jpg',
            '*.jpeg',
            '*.ico',
            '*.map'
        ],
        'except' => [
            'includes',
            'maint',
            'network',
            'user'
        ],
        'recursive' => true
    ];

}
