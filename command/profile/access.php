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

class AT_Profile_Access extends AT_Command_Abstract
{
    public function getCommand()
    {
        return 'profile:access';
    }

    public function getDescription()
    {
        return 'Manage access for profile';
    }

    public function getParams()
    {
        return array(
            array(
                'profile',
                'p',
                AT_PARAMS::REQUIRED_PARAM | AT_Params::REQUIRED_VALUE,
                'Profile name',
            ),
            array(
                'tab',
                't',
                AT_PARAMS::REQUIRED_PARAM | AT_Params::REQUIRED_VALUE,
                'Tab class name or module name',
            ),
            array(
                'display',
                'd',
                AT_Params::NO_VALUE,
                'Add display right',
            ),
            array(
                'undisplay',
                'D',
                AT_Params::NO_VALUE,
                'Remove display right',
            ),
            array(
                'add',
                'a',
                AT_Params::NO_VALUE,
                'Add add right',
            ),
            array(
                'unadd',
                'A',
                AT_Params::NO_VALUE,
                'Remove add right',
            ),
            array(
                'edit',
                'e',
                AT_Params::NO_VALUE,
                'Add edition right',
            ),
            array(
                'unedit',
                'E',
                AT_Params::NO_VALUE,
                'Remove edition right',
            ),
            array(
                'remove',
                'r',
                AT_Params::NO_VALUE,
                'Add right',
            ),
            array(
                'unremove',
                'R',
                AT_Params::NO_VALUE,
                'Remove remove right',
            ),
        );
    }


    public function run($params)
    {
        $profile = $this->getProfileIdByName($params->profile);
        if (!$profile) {
            $this->fatal(
                "« {$params->profile} » is not a valid profile.",
                4
            );
        }

        if (property_exists($params, 'display')
            && property_exists($params, 'undisplay')
        ) {
            $this->fatal(
                "You cannot add and remove display right.",
                5
            );
        }

        if (property_exists($params, 'add')
            && property_exists($params, 'unadd')
        ) {
            $this->fatal(
                "You cannot add and remove add right.",
                5
            );
        }

        if (property_exists($params, 'edit')
            && property_exists($params, 'unedit')
        ) {
            $this->fatal(
                "You cannot add and remove edit right.",
                5
            );
        }

        if (property_exists($params, 'remove')
            && property_exists($params, 'unremove')
        ) {
            $this->fatal(
                "You cannot add and remove remove right.",
                5
            );
        }

        if (strpos($params->tab, 'Admin') === 0) {
            $this->processTab($params, $profile);
        } else {
            $this->processModule($params, $profile);
        }

        $this->success("Access modified for profile #{$profile}.");
    }

    protected function processTab($params, $profile)
    {
        $tabs = $this->getTabs($params->tab);
        if (empty($tabs)) {
            $this->fatal(
                "« {$params->tab} » is not a valid tab class name.",
                6
            );
        }

        foreach ($tabs as $tab) {
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                $exists = Db::getInstance()->getValue('SELECT count(*) FROM `'._DB_PREFIX_.'access` WHERE `id_profile` = '.(int)$profile.' AND `id_tab` = '.(int)$tab);

                if ($exists) {
                    // 1.6
                    $result = true;
                    if (property_exists($params, 'display') || property_exists($params, 'undisplay')) {
                        $result &= Db::getInstance()->execute(
                            'UPDATE `'._DB_PREFIX_.'access`
                            SET
                                `view` = '.(property_exists($params, 'display') ? 1 : 0).'
                            WHERE
                            `id_profile` = '.(int)$profile.'
                            AND
                            `id_tab` = '.(int)$tab
                        );
                    }
                    if (property_exists($params, 'add') || property_exists($params, 'unadd')) {
                        $result &= Db::getInstance()->execute(
                            'UPDATE `'._DB_PREFIX_.'access`
                            SET
                                `add` = '.(property_exists($params, 'add') ? 1 : 0).'
                            WHERE
                            `id_profile` = '.(int)$profile.'
                            AND
                            `id_tab` = '.(int)$tab
                        );
                    }
                    if (property_exists($params, 'edit') || property_exists($params, 'unedit')) {
                        $result &= Db::getInstance()->execute(
                            'UPDATE `'._DB_PREFIX_.'access`
                            SET
                                `edit` = '.(property_exists($params, 'edit') ? 1 : 0).'
                            WHERE
                            `id_profile` = '.(int)$profile.'
                            AND
                            `id_tab` = '.(int)$tab
                        );
                    }
                    if (property_exists($params, 'remove') || property_exists($params, 'unremove')) {
                        $result &= Db::getInstance()->execute(
                            'UPDATE `'._DB_PREFIX_.'access`
                            SET
                                `delete` = '.(property_exists($params, 'remove') ? 1 : 0).'
                            WHERE
                            `id_profile` = '.(int)$profile.'
                            AND
                            `id_tab` = '.(int)$tab
                        );
                    }

                    if (!$result) {
                        $this->fatal(
                            "Error durring sql query.",
                            8
                        );
                    }
                } else {
                    $result = Db::getInstance()->execute(
                        'INSERT INTO `'._DB_PREFIX_.'access`
                        SET
                            `id_profile` = '.(int)$profile.',
                            `id_tab` = '.(int)$tab.',
                            `view` = '.(property_exists($params, 'display') ? 1 : 0).',
                            `add` = '.(property_exists($params, 'add') ? 1 : 0).',
                            `edit` = '.(property_exists($params, 'edit') ? 1 : 0).',
                            `delete` = '.(property_exists($params, 'remove') ? 1 : 0)
                    );

                    if (!$result) {
                        $this->fatal(
                            "Error durring sql query.",
                            9
                        );
                    }
                }
            } else {
                // 1.7 and >
                $result = true;
                if (property_exists($params, 'display')) {
                    $result &= Db::getInstance()->execute(
                        'INSERT IGNORE INTO `'._DB_PREFIX_.'access`
                        SET
                        `id_profile` = '.(int)$profile.',
                        `id_authorization_role` = "'.$this->getTabRole($tab, 'READ').'"'
                    );
                }
                if (property_exists($params, 'undisplay')) {
                    $result &= Db::getInstance()->execute(
                        'DELETE FROM `'._DB_PREFIX_.'access`
                        WHERE
                        `id_profile` = '.(int)$profile.'
                        AND
                        `id_authorization_role` = "'.$this->getTabRole($tab, 'READ').'"'
                    );
                }
                if (property_exists($params, 'add')) {
                    $result &= Db::getInstance()->execute(
                        'INSERT IGNORE INTO `'._DB_PREFIX_.'access`
                        SET
                        `id_profile` = '.(int)$profile.',
                        `id_authorization_role` = "'.$this->getTabRole($tab, 'CREATE').'"'
                    );
                }
                if (property_exists($params, 'unadd')) {
                    $result &= Db::getInstance()->execute(
                        'DELETE FROM `'._DB_PREFIX_.'access`
                        WHERE
                        `id_profile` = '.(int)$profile.'
                        AND
                        `id_authorization_role` = "'.$this->getTabRole($tab, 'CREATE').'"'
                    );
                }
                if (property_exists($params, 'edit')) {
                    $result &= Db::getInstance()->execute(
                        'INSERT IGNORE INTO `'._DB_PREFIX_.'access`
                        SET
                        `id_profile` = '.(int)$profile.',
                        `id_authorization_role` = "'.$this->getTabRole($tab, 'UPDATE').'"'
                    );
                }
                if (property_exists($params, 'unedit')) {
                    $result &= Db::getInstance()->execute(
                        'DELETE FROM `'._DB_PREFIX_.'access`
                        WHERE
                        `id_profile` = '.(int)$profile.'
                        AND
                        `id_authorization_role` = "'.$this->getTabRole($tab, 'UPDATE').'"'
                    );
                }
                if (property_exists($params, 'remove')) {
                    $result &= Db::getInstance()->execute(
                        'INSERT IGNORE INTO `'._DB_PREFIX_.'access`
                        SET
                        `id_profile` = '.(int)$profile.',
                        `id_authorization_role` = "'.$this->getTabRole($tab, 'DELETE').'"'
                    );
                }
                if (property_exists($params, 'unremove')) {
                    $result &= Db::getInstance()->execute(
                        'DELETE FROM `'._DB_PREFIX_.'access`
                        WHERE
                        `id_profile` = '.(int)$profile.'
                        AND
                        `id_authorization_role` = "'.$this->getTabRole($tab, 'DELETE').'"'
                    );
                }
            }
        }
    }

    protected function getTabRole($tab, $action)
    {
        return Db::getInstance()->getValue(
            'SELECT `id_authorization_role`
            FROM `'._DB_PREFIX_.'authorization_role`
            WHERE `slug` = "ROLE_MOD_TAB_'.strtoupper($tab).'_'.$action.'"'
        );
    }

    protected function getModuleRole($module, $action)
    {
        return Db::getInstance()->getValue(
            'SELECT `id_authorization_role`
            FROM `'._DB_PREFIX_.'authorization_role`
            WHERE `slug` = "ROLE_MOD_MODULE_'.strtoupper($module).'_'.$action.'"'
        );
    }

    protected function processModule($params, $profile)
    {
        $modules = $this->getModules($params->tab);
        if (empty($modules)) {
            $this->fatal(
                "« {$params->tab} » is not a valid module name.",
                7
            );
        }

        if (property_exists($params, 'add')
            || property_exists($params, 'unadd')
        ) {
            $this->fatal(
                "You cannot add or remove add right for module. Use edit instead.",
                10
            );
        }

        foreach ($modules as $module) {
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                $exists = Db::getInstance()->getValue('SELECT count(*) FROM `'._DB_PREFIX_.'module_access` WHERE `id_profile` = '.(int)$profile.' AND `id_module` = '.(int)$module);
                if ($exists) {
                    $result = true;
                    if (property_exists($params, 'display') || property_exists($params, 'undisplay')) {
                        $result &= Db::getInstance()->execute(
                            'UPDATE `'._DB_PREFIX_.'module_access`
                            SET
                                `view` = '.(property_exists($params, 'display') ? 1 : 0).'
                            WHERE
                            `id_profile` = '.(int)$profile.'
                            AND
                            `id_module` = '.(int)$module
                        );
                    }
                    if (property_exists($params, 'edit') || property_exists($params, 'unedit')) {
                        $result &= Db::getInstance()->execute(
                            'UPDATE `'._DB_PREFIX_.'module_access`
                            SET
                                `configure` = '.(property_exists($params, 'edit') ? 1 : 0).'
                            WHERE
                            `id_profile` = '.(int)$profile.'
                            AND
                            `id_module` = '.(int)$module
                        );
                    }
                    if (property_exists($params, 'remove') || property_exists($params, 'unremove')) {
                        $result &= Db::getInstance()->execute(
                            'UPDATE `'._DB_PREFIX_.'module_access`
                            SET
                                `uninstall` = '.(property_exists($params, 'remove') ? 1 : 0).'
                            WHERE
                            `id_profile` = '.(int)$profile.'
                            AND
                            `id_module` = '.(int)$module
                        );
                    }

                    if (!$result) {
                        $this->fatal(
                            "Error durring sql query.",
                            8
                        );
                    }
                } else {
                    $result = Db::getInstance()->execute(
                        'INSERT INTO `'._DB_PREFIX_.'module_access`
                        SET
                            `id_profile` = '.(int)$profile.',
                            `id_module` = '.(int)$module.',
                            `view` = '.(property_exists($params, 'display') ? 1 : 0).',
                            `configure` = '.(property_exists($params, 'edit') ? 1 : 0).',
                            `uninstall` = '.(property_exists($params, 'remove') ? 1 : 0)
                    );

                    if (!$result) {
                        $this->fatal(
                            "Error durring sql query.",
                            9
                        );
                    }
                }
            } else {
                // 1.7 and >
                $result = true;
                if (property_exists($params, 'display')) {
                    $result &= Db::getInstance()->execute(
                        'INSERT IGNORE INTO `'._DB_PREFIX_.'module_access`
                        SET
                        `id_profile` = '.(int)$profile.',
                        `id_authorization_role` = "'.$this->getModuleRole($module, 'READ').'"'
                    );
                }
                if (property_exists($params, 'undisplay')) {
                    $result &= Db::getInstance()->execute(
                        'DELETE FROM `'._DB_PREFIX_.'module_access`
                        WHERE
                        `id_profile` = '.(int)$profile.'
                        AND
                        `id_authorization_role` = "'.$this->getModuleRole($module, 'READ').'"'
                    );
                }
                if (property_exists($params, 'add')) {
                    $result &= Db::getInstance()->execute(
                        'INSERT IGNORE INTO `'._DB_PREFIX_.'module_access`
                        SET
                        `id_profile` = '.(int)$profile.',
                        `id_authorization_role` = "'.$this->getModuleRole($module, 'CREATE').'"'
                    );
                }
                if (property_exists($params, 'unadd')) {
                    $result &= Db::getInstance()->execute(
                        'DELETE FROM `'._DB_PREFIX_.'module_access`
                        WHERE
                        `id_profile` = '.(int)$profile.'
                        AND
                        `id_authorization_role` = "'.$this->getModuleRole($module, 'CREATE').'"'
                    );
                }
                if (property_exists($params, 'edit')) {
                    $result &= Db::getInstance()->execute(
                        'INSERT IGNORE INTO `'._DB_PREFIX_.'module_access`
                        SET
                        `id_profile` = '.(int)$profile.',
                        `id_authorization_role` = "'.$this->getModuleRole($module, 'UPDATE').'"'
                    );
                }
                if (property_exists($params, 'unedit')) {
                    $result &= Db::getInstance()->execute(
                        'DELETE FROM `'._DB_PREFIX_.'module_access`
                        WHERE
                        `id_profile` = '.(int)$profile.'
                        AND
                        `id_authorization_role` = "'.$this->getModuleRole($module, 'UPDATE').'"'
                    );
                }
                if (property_exists($params, 'remove')) {
                    $result &= Db::getInstance()->execute(
                        'INSERT IGNORE INTO `'._DB_PREFIX_.'module_access`
                        SET
                        `id_profile` = '.(int)$profile.',
                        `id_authorization_role` = "'.$this->getModuleRole($module, 'DELETE').'"'
                    );
                }
                if (property_exists($params, 'unremove')) {
                    $result &= Db::getInstance()->execute(
                        'DELETE FROM `'._DB_PREFIX_.'module_access`
                        WHERE
                        `id_profile` = '.(int)$profile.'
                        AND
                        `id_authorization_role` = "'.$this->getModuleRole($module, 'DELETE').'"'
                    );
                }
            }
        }
    }

    protected function getTabs($class)
    {
        $results = Db::getInstance()->executeS('SELECT `id_tab`, `class_name` FROM `'._DB_PREFIX_.'tab` WHERE `class_name` like \''.pSQL($class).'\'');
        $ids = array();
        $classes = array();

        $this->normal('List of tab concerned:');
        foreach ($results as $result) {
            $ids[] = $result['id_tab'];
            $classes[] = $result['class_name'];
            $this->normal(' - '.$result['class_name']);
        }

        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            return $ids;
        } else {
            return $classes;
        }
    }

    protected function getModules($name)
    {
        $results = Db::getInstance()->executeS('SELECT `id_module`, `name` FROM `'._DB_PREFIX_.'module` WHERE `name` like \''.pSQL($name).'\'');
        $ids = array();
        $modules = array();

        $this->normal('List of module concerned:');
        foreach ($results as $result) {
            $ids[] = $result['id_module'];
            $modules[] = $result['name'];
            $this->normal(' - '.$result['name']);
        }

        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            return $ids;
        } else {
            return $modules;
        }
    }

    protected function getProfileIdByName($name)
    {
        $id = Db::getInstance()->getValue('SELECT `id_profile` FROM `'._DB_PREFIX_.'profile_lang` WHERE `name` = \''.pSQL($name).'\'');

        return $id;
    }
}

