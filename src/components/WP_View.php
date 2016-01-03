<?php

namespace appitnetwork\wpthemes\components;

use Yii;
use yii\helpers\Html;
use yii\helpers\FileHelper;
use yii\helpers\ArrayHelper;
use yii\base\InvalidCallException;
use yii\base\InvalidParamException;
use yii\base\InvalidConfigException;
use yii\web\JqueryAsset;

class WP_View extends \yii\web\View
{

    public $assetBundles = [];
    /**
     * @var string the page title
     */
    public $title;
    /**
     * @var array the registered meta tags.
     * @see registerMetaTag()
     */
    public $metaTags;
    /**
     * @var array the registered link tags.
     * @see registerLinkTag()
     */
    public $linkTags;
    /**
     * @var array the registered CSS code blocks.
     * @see registerCss()
     */
    public $css;
    /**
     * @var array the registered CSS files.
     * @see registerCssFile()
     */
    public $cssFiles;
    /**
     * @var array the registered JS code blocks
     * @see registerJs()
     */
    public $js;
    /**
     * @var array the registered JS files.
     * @see registerJsFile()
     */
    public $jsFiles;

    public $content;

    private $_assetManager;

    private $_viewFiles = [];

    /**
     * Marks the position of an HTML head section.
     */
    public function head()
    {
        echo parent::PH_HEAD;
    }

    /**
     * Marks the beginning of an HTML body section.
     */
    public function beginBody()
    {
        echo parent::PH_BODY_BEGIN;
        $this->trigger(parent::EVENT_BEGIN_BODY);
    }

    /**
     * Marks the ending of an HTML body section.
     */
    public function endBody()
    {
        $this->trigger(parent::EVENT_END_BODY);
        echo parent::PH_BODY_END;

        foreach (array_keys($this->assetBundles) as $bundle) {
            $this->registerAssetFiles($bundle);
        }
    }

    /**
     * Marks the ending of an HTML page.
     * @param boolean $ajaxMode whether the view is rendering in AJAX mode.
     * If true, the JS scripts registered at [[POS_READY]] and [[POS_LOAD]] positions
     * will be rendered at the end of the view like normal scripts.
     */
    public function endPage($ajaxMode = false)
    {
        $this->trigger(parent::EVENT_END_PAGE);

        $content = ob_get_clean();

        echo strtr($content, [
            parent::PH_HEAD => $this->renderHeadHtml(),
            parent::PH_BODY_BEGIN => $this->renderBodyBeginHtml(),
            parent::PH_BODY_END => $this->renderBodyEndHtml($ajaxMode),
        ]);

        $this->clear();
    }

    public function renderAjax($view, $params = [], $context = null)
    {
        $viewFile = $this->findViewFile($view, $context);

        ob_start();
        ob_implicit_flush(false);

        $this->beginPage();
        $this->head();
        $this->beginBody();
        echo $this->renderFile($viewFile, $params, $context);
        $this->endBody();
        $this->endPage(true);

        return ob_get_clean();
    }

    public function getAssetManager()
    {
        return $this->_assetManager ?: Yii::$app->getAssetManager();
    }

    public function setAssetManager($value)
    {
        $this->_assetManager = $value;
    }

    public function clear()
    {
        $this->metaTags = null;
        $this->linkTags = null;
        $this->css = null;
        $this->cssFiles = null;
        $this->js = null;
        $this->jsFiles = null;
        $this->assetBundles = [];
    }

    protected function registerAssetFiles($name)
    {
        if (!isset($this->assetBundles[$name])) {
            return;
        }
        $bundle = $this->assetBundles[$name];
        if ($bundle) {
            foreach ($bundle->depends as $dep) {
                $this->registerAssetFiles($dep);
            }
            $bundle->registerAssetFiles($this);
        }
        unset($this->assetBundles[$name]);
    }

    public function registerAssetBundle($name, $position = null)
    {
        if (!isset($this->assetBundles[$name])) {
            $am = $this->getAssetManager();
            $bundle = $am->getBundle($name);
            $this->assetBundles[$name] = false;
            // register dependencies
            $pos = isset($bundle->jsOptions['position']) ? $bundle->jsOptions['position'] : null;
            foreach ($bundle->depends as $dep) {
                $this->registerAssetBundle($dep, $pos);
            }
            $this->assetBundles[$name] = $bundle;
        } elseif ($this->assetBundles[$name] === false) {
            throw new InvalidConfigException("A circular dependency is detected for bundle '$name'.");
        } else {
            $bundle = $this->assetBundles[$name];
        }

        if ($position !== null) {
            $pos = isset($bundle->jsOptions['position']) ? $bundle->jsOptions['position'] : null;
            if ($pos === null) {
                $bundle->jsOptions['position'] = $pos = $position;
            } elseif ($pos > $position) {
                throw new InvalidConfigException("An asset bundle that depends on '$name' has a higher javascript file position configured than '$name'.");
            }
            // update position for all dependencies
            foreach ($bundle->depends as $dep) {
                $this->registerAssetBundle($dep, $pos);
            }
        }

        return $bundle;
    }

    public function registerMetaTag($options, $key = null)
    {
        if ($key === null) {
            $this->metaTags[] = Html::tag('meta', '', $options);
        } else {
            $this->metaTags[$key] = Html::tag('meta', '', $options);
        }
    }

    public function registerLinkTag($options, $key = null)
    {
        if ($key === null) {
            $this->linkTags[] = Html::tag('link', '', $options);
        } else {
            $this->linkTags[$key] = Html::tag('link', '', $options);
        }
    }

    public function registerCss($css, $options = [], $key = null)
    {
        $key = $key ?: md5($css);
        $this->css[$key] = Html::style($css, $options);
    }

    public function registerCssFile($url, $options = [], $key = null)
    {
        $url = Yii::getAlias($url);
        $key = $key ?: $url;
        $depends = ArrayHelper::remove($options, 'depends', []);

        if (empty($depends)) {
            $this->cssFiles[$key] = Html::cssFile($url, $options);
        } else {
            $this->getAssetManager()->bundles[$key] = new AssetBundle([
                'baseUrl' => '',
                'css' => [strncmp($url, '//', 2) === 0 ? $url : ltrim($url, '/')],
                'cssOptions' => $options,
                'depends' => (array) $depends,
            ]);
            $this->registerAssetBundle($key);
        }
    }

    public function registerJs($js, $position = parent::POS_READY, $key = null)
    {
        $key = $key ?: md5($js);
        $this->js[$position][$key] = $js;
        if ($position === parent::POS_READY || $position === parent::POS_LOAD) {
            JqueryAsset::register($this);
        }
    }

    public function registerJsFile($url, $options = [], $key = null)
    {
        $url = Yii::getAlias($url);
        $key = $key ?: $url;
        $depends = ArrayHelper::remove($options, 'depends', []);

        if (empty($depends)) {
            $position = ArrayHelper::remove($options, 'position', parent::POS_END);
            $this->jsFiles[$position][$key] = Html::jsFile($url, $options);
        } else {
            $this->getAssetManager()->bundles[$key] = new AssetBundle([
                'baseUrl' => '',
                'js' => [strncmp($url, '//', 2) === 0 ? $url : ltrim($url, '/')],
                'jsOptions' => $options,
                'depends' => (array) $depends,
            ]);
            $this->registerAssetBundle($key);
        }
    }

    protected function renderHeadHtml()
    {
        $lines = [];
        if (!empty($this->metaTags)) {
            $lines[] = implode("\n", $this->metaTags);
        }

        if (!empty($this->linkTags)) {
            $lines[] = implode("\n", $this->linkTags);
        }
        if (!empty($this->cssFiles)) {
            $lines[] = implode("\n", $this->cssFiles);
        }
        if (!empty($this->css)) {
            $lines[] = implode("\n", $this->css);
        }
        if (!empty($this->jsFiles[parent::POS_HEAD])) {
            $lines[] = implode("\n", $this->jsFiles[parent::POS_HEAD]);
        }
        if (!empty($this->js[parent::POS_HEAD])) {
            $lines[] = Html::script(implode("\n", $this->js[parent::POS_HEAD]), ['type' => 'text/javascript']);
        }

        return empty($lines) ? '' : implode("\n", $lines);
    }

    protected function renderBodyBeginHtml()
    {
        $lines = [];
        if (!empty($this->jsFiles[parent::POS_BEGIN])) {
            $lines[] = implode("\n", $this->jsFiles[parent::POS_BEGIN]);
        }
        if (!empty($this->js[parent::POS_BEGIN])) {
            $lines[] = Html::script(implode("\n", $this->js[parent::POS_BEGIN]), ['type' => 'text/javascript']);
        }

        return empty($lines) ? '' : implode("\n", $lines);
    }

    protected function renderBodyEndHtml($ajaxMode)
    {
        $lines = [];

        if (!empty($this->jsFiles[parent::POS_END])) {
            $lines[] = implode("\n", $this->jsFiles[parent::POS_END]);
        }

        if ($ajaxMode) {
            $scripts = [];
            if (!empty($this->js[parent::POS_END])) {
                $scripts[] = implode("\n", $this->js[parent::POS_END]);
            }
            if (!empty($this->js[parent::POS_READY])) {
                $scripts[] = implode("\n", $this->js[parent::POS_READY]);
            }
            if (!empty($this->js[parent::POS_LOAD])) {
                $scripts[] = implode("\n", $this->js[parent::POS_LOAD]);
            }
            if (!empty($scripts)) {
                $lines[] = Html::script(implode("\n", $scripts), ['type' => 'text/javascript']);
            }
        } else {
            if (!empty($this->js[parent::POS_END])) {
                $lines[] = Html::script(implode("\n", $this->js[parent::POS_END]), ['type' => 'text/javascript']);
            }
            if (!empty($this->js[parent::POS_READY])) {
                $js = "jQuery(document).ready(function () {\n" . implode("\n", $this->js[parent::POS_READY]) . "\n});";
                $lines[] = Html::script($js, ['type' => 'text/javascript']);
            }
            if (!empty($this->js[parent::POS_LOAD])) {
                $js = "jQuery(window).load(function () {\n" . implode("\n", $this->js[parent::POS_LOAD]) . "\n});";
                $lines[] = Html::script($js, ['type' => 'text/javascript']);
            }
        }

        return empty($lines) ? '' : implode("\n", $lines);
    }




    public function render($view, $params = [], $context = null)
    {
        // pr('render');pr($view);//die;
        $viewFile = $this->findViewFile($view, $context);
        return $this->renderFile($viewFile, $params, $context);
    }

    protected function findViewFile($view, $context = null)
    {
        // pr('findViewFile');pr($view);pr(Yii::$app->wpthemes->getThemeBasePath());die;
        // pr($this->theme->basePath);pr($view);die;

        // if ( $view != 'index' ) {

        // } else {
            
            // $path = Yii::$app->wpthemes->getWordpressLocation() . DIRECTORY_SEPARATOR . 'index.php';

            $path = '@appitnetwork/wpthemes/views/gateways/index.php';
            // $path = Yii::$app->wpthemes->getThemeBasePath() . DIRECTORY_SEPARATOR . 'index.php';

            // $path = $file . '.' . $this->defaultExtension;
            // if ($this->defaultExtension !== 'php' && !is_file($path)) {
            //     $path = $file . '.php';
            // }
            // pr($path);die;
        // }

        return $path;
        // } else {
        //     return $this->_defaultFindViewFile( $view, $context );
        // }
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

    public function renderFile($viewFile, $params = [], $context = null)
    {
        $viewFile = Yii::getAlias($viewFile);

        if ($this->theme !== null) {
            $viewFile = $this->theme->applyTo($viewFile);
        }
        if (is_file($viewFile)) {
            $viewFile = FileHelper::localize($viewFile);
        } else {
            throw new InvalidParamException("The view file does not exist: $viewFile");
        }

        $oldContext = $this->context;
        if ($context !== null) {
            $this->context = $context;
        }
        $output = '';
        $this->_viewFiles[] = $viewFile;

        if ($this->beforeRender($viewFile, $params)) {
            Yii::trace("Rendering view file: $viewFile", __METHOD__);
            $ext = pathinfo($viewFile, PATHINFO_EXTENSION);
            if (isset($this->renderers[$ext])) {
                if (is_array($this->renderers[$ext]) || is_string($this->renderers[$ext])) {
                    $this->renderers[$ext] = Yii::createObject($this->renderers[$ext]);
                }
                /* @var $renderer ViewRenderer */
                $renderer = $this->renderers[$ext];
                $output = $renderer->render($this, $viewFile, $params);
            } else {
                $output = $this->renderPhpFile($viewFile, $params);
            }
            $this->afterRender($viewFile, $params, $output);
        }

        array_pop($this->_viewFiles);
        $this->context = $oldContext;

        return $output;
    }

    public function afterRender($viewFile, $params, &$output)
    {
        // pr('afterRender');pr($viewFile);//die;
        if (parent::afterRender($viewFile, $params, $output)) {
            return true;
        }
        return false;
    }

}
