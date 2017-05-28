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

class AT_Profile_Create extends AT_Command_Abstract
{
    public function getCommand()
    {
        return 'profile:create';
    }

    public function getDescription()
    {
        return 'Create profile';
    }

    public function getParams()
    {
        return array(
            array(
                'name',
                'n',
                AT_PARAMS::REQUIRED_PARAM | AT_Params::REQUIRED_VALUE,
                'Name',
            ),
        );
    }


    public function run($params)
    {
        $profile = new Profile();

        $name = array();
        foreach (Language::getIds() as $id) {
            $name[$id] = $params->name;
        }

        $profile->name = $name;
        if (!$profile->save()) {
            $this->fatal(
                "Profile not created.",
                4
            );
        }
        $this->success("Profile #{$profile->id} was created.");
    }
}

