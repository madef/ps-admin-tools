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

class AT_Command
{
    private $commands;

    public function __construct($argv)
    {
        AT_Params::setInstance($argv);

        if (!file_exists(dirname(__FILE__).'/../cache/commands.json')) {
            $this->refreshCache();
        }

        $this->commands = json_decode(
            file_get_contents(
                dirname(__FILE__).'/../cache/commands.json'
            ),
            true
        );
    }

    public function run()
    {
        $command = AT_Params::getInstance()->getCommand();
        if (is_null($command)) {
            AT_Message::getInstance()->error('Missing command.');
            AT_Message::getInstance()->normal('');
            AT_Message::getInstance()->normal('Available commands:');
            foreach ($this->commands as $command => $details) {
                AT_Message::getInstance()->normal('  '.$command.'  '.$details['desc']);
            }
            AT_Message::getInstance()->normal('');
            die(1);
        }

        if (!isset($this->commands[$command])) {
            AT_Message::getInstance()->error('Unknow command « '.$command.' ».');
            die(2);
        }

        require_once(dirname(__FILE__).'/../command/'.$this->commands[$command]['file']);
        $command = new $this->commands[$command]['classname']();
        $command->execute();
    }

    public function refreshCache()
    {
        $commands = array();
        foreach (glob(dirname(__FILE__).'/../command/*/*.php') as $file) {
            if (basename($file) === 'index.php') {
                continue;
            }
            require_once($file);
            $shortFilePath = preg_replace('/^.*\/admin-tools\/.*\/command\/([^\/]*\/[^\/]*)$/', '$1', $file);
            $className = 'AT_'.str_replace(
                array('-', '/',),
                array('', '_'),
                preg_replace('/\.php$/', '', $shortFilePath)
            );

            if (!is_subclass_of($className, 'AT_COMMAND_ABSTRACT')) {
                continue;
            }

            $instance = new $className();

            $commands[$instance->getCommand()] = array(
                'file' => $shortFilePath,
                'desc' => $instance->getDescription(),
                'classname' => $className,
                'params' => $instance->getParams(),
            );
        }

        file_put_contents(
            dirname(__FILE__).'/../cache/commands.json',
            json_encode($commands)
        );
    }
}

