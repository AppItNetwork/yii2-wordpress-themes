<?php

namespace appitnetwork\wpthemes\assets;

use Yii;
use yii\web\AssetBundle;
use yii\helpers\FileHelper;

class WP_Asset extends AssetBundle
{
    public $sourcePath = '@vendor/appitnetwork/yii2-wordpress-themes/src/wordpress/wp-includes';
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
            'certificates',
            'pomo',
            'customize',
            'ID3',
            'random_compat',
            'rest-api',
            'SimplePie',
            'Text',
            'theme-compat',
            'widgets'
        ],
        'recursive' => true
    ];

}
