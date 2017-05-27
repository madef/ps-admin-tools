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

class AT_Module_Install extends AT_Command_Abstract
{
    public function getCommand()
    {
        return 'module:install';
    }

    public function getDescription()
    {
        return 'Install module';
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
            array(
                'shop',
                's',
                AT_Params::REQUIRED_VALUE | AT_Params::MULTIPLE_VALUE,
                'Shop Id',
            ),
        );
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

        if (!$isInstalled && !$module->install()) {
            $this->fatal(
                "Cannot install the module « {$params->module} ».",
                5
            );
        }


        if (empty($params->shop)) {
            foreach (Shop::getShopsCollection(false) as $shop) {
                Db::getInstance()->insert(
                    'module_shop',
                    array(
                        'id_module' => (int) $module->id,
                        'id_shop' => (int) $shop->id,
                        'enable_device' => 7,
                    )
                );
            }
        } else {
            foreach ($params->shop as $shop) {
                Db::getInstance()->insert(
                    'module_shop',
                    array(
                        'id_module' => (int) $module->id,
                        'id_shop' => (int) $shop,
                        'enable_device' => 7,
                    )
                );
            }
        }

        if ($isInstalled) {
            $this->note("Module « {$params->module} » is already installed.");
        } else {
            $this->success("Module « {$params->module} » was installed.");
        }
    }
}

