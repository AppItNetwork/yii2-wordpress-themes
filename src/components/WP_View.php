<?php

namespace appitnetwork\wpthemes\components;

use Yii;
use yii\helpers\Html;
use yii\helpers\FileHelper;
use yii\helpers\ArrayHelper;
use yii\base\InvalidCallException;
use yii\base\InvalidParamException;
use yii\base\InvalidConfigException;
use yii\base\ViewContextInterface;
use yii\web\JqueryAsset;

class WP_View extends \yii\web\View
{

    public $content;
    public $wp_query;

    public $originalTheme;
    public $wpTheme;

    private $_assetManager;
    private $_viewFiles = [];

    public function init()
    {
        parent::init();
        $this->wpTheme = $this->theme;
    }

    protected function findViewFile($view, $context = null)
    {
        // pr('findViewFile');pr($view);pr($context->module->id);die;
        // pr($this->theme->basePath);pr($view);die;

        if ( !empty($context->module) && $context->module->id != 'debug' && $context->module->id != 'gii' ) {

            if ( !($this->wpTheme instanceof WP_Theme) ) {
                $this->theme = $this->wpTheme;
            }

            $path = '@appitnetwork/wpthemes/views/gateways/index.php';
            return $path;

        } else {

            // $this->wpTheme = $this->theme;
            $this->theme = $this->originalTheme;
            return $this->_defaultFindViewFile( $view, $context );

        }
    }

    private function _defaultFindViewFile($view, $context = null)
    {
        if (strncmp($view, '@', 1) === 0) {
            // e.g. "@app/views/main"
            $file = Yii::getAlias($view);
        } elseif (strncmp($view, '//', 2) === 0) {
            // e.g. "//layouts/main"
            $file = Yii::$app->getViewPath() . DIRECTORY_SEPARATOR . ltrim($view, '/');
        } elseif (strncmp($view, '/', 1) === 0) {
            // e.g. "/site/index"
            if (Yii::$app->controller !== null) {
                $file = Yii::$app->controller->module->getViewPath() . DIRECTORY_SEPARATOR . ltrim($view, '/');
            } else {
                throw new InvalidCallException("Unable to locate view file for view '$view': no active controller.");
            }
        } elseif ($context instanceof ViewContextInterface) {
            $file = $context->getViewPath() . DIRECTORY_SEPARATOR . $view;
        } elseif (($currentViewFile = $this->getViewFile()) !== false) {
            $file = dirname($currentViewFile) . DIRECTORY_SEPARATOR . $view;
        } else {
            throw new InvalidCallException("Unable to resolve view file for view '$view': no active view context.");
        }

        if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
            return $file;
        }
        $path = $file . '.' . $this->defaultExtension;
        if ($this->defaultExtension !== 'php' && !is_file($path)) {
            $path = $file . '.php';
        }

        return $path;
    }

}
