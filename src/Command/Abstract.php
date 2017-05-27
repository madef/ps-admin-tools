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

abstract class AT_Command_Abstract
{
    public abstract function getCommand();
    public abstract function getDescription();
    public abstract function run($params);

    public function getParams()
    {
        return array();
    }

    protected function success($message)
    {
        AT_Message::getInstance()->success($message);
    }

    protected function warning($message)
    {
        AT_Message::getInstance()->warning($message);
    }

    protected function note($message)
    {
        AT_Message::getInstance()->note($message);
    }

    protected function error($message)
    {
        AT_Message::getInstance()->error($message);
    }

    protected function fatal($message, $errorCode)
    {
        AT_Message::getInstance()->error($message);
        die((int) $errorCode);
    }


    protected function normal($message)
    {
        AT_Message::getInstance()->normal($message);
    }

    protected function display($message, $code)
    {
        if ($code === self::NORMAL) {
            echo $message."\n";
        } else {
            echo chr(27).$code.$message.chr(27).'[0m'."\n";
        }
    }

    public final function execute()
    {
        try {
            $this->run(
                AT_PARAMS::getInstance()->getValues(
                    $this->getParams()
                )
            );
            die(0);
        } catch (Exception $e) {
            $this->fatal('Fatal Error: '.$e->getMessage(), 3);
        }
    }
}

