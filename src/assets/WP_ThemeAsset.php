<?php

namespace appitnetwork\wpthemes\assets;

use Yii;
use yii\web\AssetBundle;
use yii\helpers\FileHelper;

class WP_ThemeAsset extends AssetBundle
{
    public $sourcePath = '@vendor/appitnetwork/yii2-wordpress-themes/src/wordpress/wp-content/themes';
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
            'inc/',
            'languages/',
            'page-templates/',
            'template-parts/'
        ]
    ];

    public function init() {
        
        // pr(Yii::$app->view->theme->selectedTheme);die;
        // $this->js = $this->_populateFiles('js');
        // $this->css = $this->_populateFiles('css');

        parent::init();
    }

    private function _populateFiles($type)
    {
        $selectedTheme = Yii::$app->view->theme->selectedTheme;
        $path = $this->sourcePath . DIRECTORY_SEPARATOR . $selectedTheme . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR;
        $files = FileHelper::findFiles( Yii::getAlias($path), ['only'=>['*.'.$type]] );
        $trimmedFiles = [];
        if ( isset($files[0]) ) {
            foreach ( $files as $index => $file ) {
                $trimmedFiles[] = str_replace(Yii::getAlias($this->sourcePath . DIRECTORY_SEPARATOR), '', $file);
            }
        }
        
        return $trimmedFiles;
    }
}
