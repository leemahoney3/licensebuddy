<?php

namespace LicenseBuddy\Addon\Helpers;

/**
 * License Buddy Addon
 *
 * A software licensing solution for WHMCS
 *
 * @package    LicenseBuddy
 * @author     Lee Mahoney <lee@leemahoney.dev>
 * @copyright  Copyright (c) Lee Mahoney 2022
 * @license    MIT License
 * @version    1.0.0
 * @link       https://leemahoney.dev
 */

class TemplateHelper {

    protected static $baseDir = __DIR__ . '/../../templates/';

    public static function checkExists($templateName) {

        $templateName = str_replace('.', '/', $templateName) . '.tpl';

        return (file_exists(self::$baseDir . $templateName)) ? true : false;

    }

    public static function getTemplate($templateName, $args = []) {

        $newTemplateName = str_replace('.', '/', $templateName) . '.tpl';

        $smarty                 = new \Smarty;
        $smarty->caching        = false;
        $smarty->compile_dir    = $GLOBALS['templates_compiledir'];
        $smarty->template_dir   = self::$baseDir;
        $smarty->registerClass('PageHelper', '\LicenseBuddy\Addon\Helpers\AdminPageHelper');

        if (self::checkExists($templateName)) {

            foreach ($args as $arg => $value) {
                $smarty->assign($arg, $value);
            }

            $smarty->display($newTemplateName);

        } else {

            $templateName = explode('.', '/', $templateName)[0] . '/notfound.tpl';
            $smarty->display($templateName);

        }

    }

}