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

class AT_Module_Upgrade extends AT_Command_Abstract
{
    public function getCommand()
    {
        return 'module:upgrade';
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
        return 'Upgrade module';
    }

    public function run($params)
    {
        $objectModule = Module::getInstanceByName($params->module);

        if (!$objectModule) {
            $this->fatal(
                "Cannot find the module « {$params->module} ».",
                4
            );
        }

        $isInstalled = Module::isInstalled($params->module);
        if (!$isInstalled) {
            $this->fatal("Module « {$params->module} » is not installed.", 5);
        }

        $isEnabled = Module::isEnabled($params->module);
        if (!$isEnabled) {
            $this->fatal("Module « {$params->module} » is not enabled.", 6);
        }

        $module = $this->getModuleFormDatabase($params->module);
        if (Module::initUpgradeModule($module)) {
            //$object = Adapter_ServiceLocator::get($module->name);
            $objectModule->runUpgradeModule();
            if ((count($errors = $objectModule->getErrors()))) {
                $this->fatal(
                    "Cannot upgrade the module « {$params->module} » : "
                    .implode(', ',$errors),
                    7
                );
            }
            $this->success("Module « {$params->module} » was upgraded.");
            foreach ($objectModule->getConfirmations() as $confirmation) {
                $this->success($confirmation);
            }
        } else {
            $this->note("Module « {$params->module} » is up-to-date.");
        }
    }

    protected function getModuleFormDatabase($moduleName)
    {
        $modules = Module::getModulesOnDisk(true, false, 0);
        foreach ($modules as $module) {
            if ($module->name === $moduleName) {
                return $module;
            }
        }

        $this->fatal(
            "Cannot find module named « {$params->module} ».",
            5
        );
    }
}

