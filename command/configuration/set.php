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

class AT_Configuration_Set extends AT_Command_Abstract
{
    public function getCommand()
    {
        return 'configuration:set';
    }

    public function getParams()
    {
        return array(
            array(
                'key',
                'k',
                AT_Params::REQUIRED_VALUE | AT_Params::REQUIRED_PARAM,
                'Configuration key',
            ),
            array(
                'value',
                'v',
                AT_Params::REQUIRED_VALUE | AT_Params::REQUIRED_PARAM,
                'Configuration value',
            ),
            array(
                'html',
                'h',
                AT_Params::NO_VALUE,
                'Is html',
            ),
            array(
                'group',
                'g',
                AT_Params::REQUIRED_VALUE | AT_Params::MULTIPLE_VALUE,
                'Shop group ID',
            ),
            array(
                'shop',
                's',
                AT_Params::REQUIRED_VALUE | AT_Params::MULTIPLE_VALUE,
                'Shop ID',
            ),
        );
    }

    public function getDescription()
    {
        return 'Create or update configuration value';
    }

    public function run($params)
    {
        $html = isset($params->html);

        if (!isset($params->shop)) {
            $shops =  array(0);
        } else {
            $shops = $params->shop;
        }

        if (!isset($params->group)) {
            $groups =  array(0);
        } else {
            $groups = $params->group;
        }

        foreach ($shops as $shop) {
            foreach ($groups as $group) {
                Configuration::updateValue(
                    $params->key,
                    $params->value,
                    $html,
                    $group,
                    $shop
                );
            }
        }

        $this->success('Configuration updated');
    }
}

