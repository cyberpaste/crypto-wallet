<?php

namespace app\components;

use app\base\Module;
use yii\helpers\HtmlPurifier;
use yii;

class TwigExtension extends \Twig_Extension {

    public function getGlobals() {
        return [
            'cur_year' => date('Y'),
            'uri' => $_SERVER['REQUEST_URI'],
            'host' => $_SERVER['HTTP_HOST'],
            'csrf_token' => Yii::$app->request->csrfToken,
            'csrf_param' => Yii::$app->request->csrfParam,
            'lang' => Yii::$app->language,
        ];
    }

    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('asset', [$this, 'asset']),
            new \Twig_SimpleFunction('t', [$this, 'translate']),
            new \Twig_SimpleFunction('widget', [$this, 'widget']),
            new \Twig_SimpleFunction('params', [$this, 'params']),
            new \Twig_SimpleFunction('strip_tags', [$this, 'strip_tags']),
            new \Twig_SimpleFunction('htmlPurifier', [$this, 'htmlPurifier']),
            new \Twig_SimpleFunction('jsonEncode', [$this, 'jsonEncode']),
            new \Twig_SimpleFunction('prr', [$this, 'prr']),
        ];
    }

    public function widget($path, $params = []) {
        $path = explode('/', $path);

        if (sizeof($path) == 1) {
            $name = $this->resolveClassName($path[0]);
            $module = '';
        } else {
            $module = $path[0];
            $name = $this->resolveClassName($path[1]);
        }

        if ($module && strncmp($module, '@', 1) === 0) {
            $module = str_replace('@', '\\', $module);
        } elseif ($module) {
            $module = $this->getModuleNamespace($module);
        }

        if ($module) {
            $class = $module . '\widgets\\' . $name;
        } else {
            $class = 'app\widgets\\' . $name;
        }

        /** @var yii\base\Widget $class */
        return $class::widget($params);
    }

    public function asset($name, $param = '') {
        if ($param) {
            return $this->getAsset($name)->$param;
        }

        $this->getAsset($name);

        return '';
    }

    public function strip_tags($value) {
        return strip_tags(html_entity_decode($value));
    }

    public function params($name, $value = '', $replace = true) {
        if (in_array($name, ['page_description', 'page_keywords'])) {
            $value = $this->strip_tags($value);
        }

        if ($replace) {
            Yii::$app->view->params[$name] = $value;
        } else {
            if (is_array(Yii::$app->view->params[$name])) {
                Yii::$app->view->params[$name][] = $value;
            } else {
                Yii::$app->view->params[$name] .= $value;
            }
        }
    }

    private function getAsset($_path) {
        $path = explode('/', $_path);

        if (sizeof($path) == 1) {
            $name = $this->resolveClassName($path[0]);
            $module = '';
        } else {
            $module = $path[0];
            $name = $this->resolveClassName($path[1]);
        }

        if ($module && strncmp($module, '@', 1) === 0) {
            $module = str_replace('@', '\\', $module);
        } elseif ($module) {
            $module = $this->getModuleNamespace($module);
        }

        if ($module) {
            $class = $module . '\assets\\' . $name . 'Asset';
        } else {
            $class = 'app\assets\\' . $name . 'Asset';
        }

        /** @var yii\web\AssetBundle $class */
        return $class::register(Yii::$app->view);
    }

    public function translate($_path, $message, $params = [], $language = null) {
        $path = explode('/', $_path);

        if (sizeof($path) == 1) {
            $category = $path[0];
            $module = '';
        } else {
            $module = $path[0];
            $category = $path[1];
        }

        if ($module && strncmp($module, '@', 1) === 0) {
            $module = str_replace('@', '\\', $module);
        } elseif ($module) {
            $module = Yii::$app->config->getModuleClass($module);
        }

        if ($module) {
            $class = $module;
        } else {
            $class = 'Yii';
        }

        /** @var Module $class */
        return $class::t($category, $message, $params, $language);
    }

    private function getModuleNamespace($module) {
        $root = explode($module, Yii::$app->config->getModuleClass($module));

        return $root[0] . $module;
    }

    public function md5($text) {
        return md5($text);
    }

    public function base64_encod($text) {
        return base64_encode($text);
    }

    /**
     * Resolves class name from widget and asset syntax
     *
     * @param string $className class name
     * @return string
     */
    public function resolveClassName($className) {
        $className = yii\helpers\Inflector::id2camel($className, '-');

        return $className;
    }

    public function getName() {
        return 'custom-twig';
    }

    public function htmlPurifier($html) {
        return HTMLPurifier::process($html);
    }

    public function sortHrefMaker($path, $sortedBy = 'id') {
        if (strrpos($path, 'sortedBy') && strrpos($path, 'sortedByDesc')) {
            $path = preg_replace('/(|&|\?)sortedByDesc=true/', '', $path);
            $path = preg_replace('/(|&|\?)sortedBy=' . Yii::$app->request->get('sortedBy') . '/', '', $path);

            if ($sortedBy != Yii::$app->request->get('sortedBy')) {
                if (substr($path, -1) == "/") {
                    $path .= '?sortedBy=' . $sortedBy;
                } else {
                    $path .= '&sortedBy=' . $sortedBy;
                }
            }
        } elseif (strrpos($path, 'sortedBy')) {
            $path = preg_replace('/(|&|\?)sortedBy=' . Yii::$app->request->get('sortedBy') . '/', '', $path);
            if ($sortedBy == Yii::$app->request->get('sortedBy')) {
                if (substr($path, -1) == "/") {
                    $path .= '?sortedBy=' . $sortedBy . '&sortedByDesc=true';
                } else {
                    $path .= '&sortedBy=' . $sortedBy . '&sortedByDesc=true';
                }
            } else {
                if (substr($path, -1) == "/") {
                    $path .= '?sortedBy=' . $sortedBy;
                } else {
                    $path .= '&sortedBy=' . $sortedBy;
                }
            }
        } else {
            if (substr($path, -1) == "/") {
                $path .= '?sortedBy=' . $sortedBy;
            } else {
                $path .= '&sortedBy=' . $sortedBy;
            }
        }

        return $path;
    }

    public function jsonEncode($arr) {
        return json_encode($arr);
    }

    public function prr($var = false) {
        echo "<pre style='font-size: 14px; background-color: #dbffc8; padding: 10px; margin: 5px; '>";
        print_r($var);
        echo "</pre>";
    }

}
