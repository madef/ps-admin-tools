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

class AT_Module_Uninstall extends AT_Command_Abstract
{
    public function getCommand()
    {
        return 'module:uninstall';
    }

    public function getParams()
    {
        return array(
            array(
                'module',
                'm',
                AT_Params::REQUIRED_VALUE | AT_Params::REQUIRED_PARAM,
                'Module name',
            ),
        );
    }

    public function getDescription()
    {
        return 'Uninstall module';
    }

    public function run($params)
    {
        $module = Module::getInstanceByName($params->module);

        if (!$module) {
            $this->fatal(
                "Cannot find the module « {$params->module} ».",
                4
            );
        }

        $isInstalled = Module::isInstalled($params->module);

        if ($isInstalled && !$module->uninstall()) {
            $this->fatal(
                "Cannot uninstall the module « {$params->module} ».",
                5
            );
        }

        if (!$isInstalled) {
            $this->note("Module « {$params->module} » is already uninstalled.");
        } else {
            $this->success("Module « {$params->module} » was uninstalled.");
        }
    }
}

