<?php
/**
 * 2013-2017 MADEF IT
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@madef.fr so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    MADEF IT <contact@madef.fr>
 *  @copyright 2013-2017 MADEF IT
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class AT_Message
{
    const SUCCESS = '[42m';
    const FAILURE = '[41m';
    const WARNING = '[43m';
    const NOTE = '[44m';
    const NORMAL = '';

    private static $instance = null;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function success($message)
    {
        $this->display($message, self::SUCCESS);
    }

    public function warning($message)
    {
        $this->display($message, self::WARNING);
    }

    public function note($message)
    {
        $this->display($message, self::NOTE);
    }

    public function error($message)
    {
        $this->display($message, self::FAILURE);
    }

    public function normal($message)
    {
        $this->display($message, self::NORMAL);
    }

    protected function display($message, $type)
    {
        if ($type === self::NORMAL) {
            echo $message."\n";
        } else {
            echo chr(27).$type.$message.chr(27).'[0m'."\n";
        }
    }
}

