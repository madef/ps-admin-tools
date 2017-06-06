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

class AT_ObjectModel_Add extends AT_Command_Abstract
{
    public function getCommand()
    {
        return 'objectmodel:add';
    }

    public function getParams()
    {
        return array(
            array(
                'type',
                't',
                AT_Params::REQUIRED_VALUE | AT_Params::REQUIRED_PARAM,
                'Object type',
            ),
            array(
                'value',
                'v',
                AT_Params::MULTIPLE_VALUE | AT_Params::REQUIRED_VALUE | AT_Params::REQUIRED_PARAM,
                'key:value',
            ),
            array(
                'lang',
                'l',
                AT_Params::REQUIRED_VALUE | AT_Params::OPTIONAL_PARAM,
                'Language id',
            ),
            array(
                'id',
                'i',
                AT_Params::REQUIRED_VALUE | AT_Params::OPTIONAL_PARAM,
                'Object Id',
            ),
        );
    }

    public function getDescription()
    {
        return 'Add object';
    }

    public function run($params)
    {
        $className = $params->type;
        $languages = Language::getIsoIds(false);
        $language = Context::getContext()->language->id;
        if (isset($params->lang)) {
            $language = (int) $params->lang;
        }

        $id = null;
        if (isset($params->id)) {
            $id = (int) $params->id;
        }

        if (!class_exists($className)) {
            $this->fatal(
                "Unknow object model « {$params->type} ».",
                4
            );
        }

        $object = new $className($id, null, $language);

        foreach ($params->value as $value) {
            if (strpos($value, ':') == false) { // The double equal is not a mistake
                $this->fatal(
                    "Invalid syntax for value « {$value} », the correct syntax is « attribute:value ».",
                    5
                );
            }
            $parts = explode(':', $value);
            $key = array_shift($parts);
            $value = implode($parts);

            if (!property_exists($object, $key)) {
                $this->fatal(
                    "Invalid property « {$key} ».",
                    6
                );
            }

            if (!empty($className::$definition['fields'][$key]['lang'])) {
                $values = array();
                if (is_array($object->$key)) {
                    $values = $object->$key;
                }

                $values[$language] = $value;
                foreach ($languages as $lang) {
                }
                $object->$key = $values;
            } else {
                $object->$key = $value;
            }

            $this->note("$key: $value");
        }

        $object->save();

        $this->success("Object #{$object->id} of type « {$params->type}·» created.");
    }
}

