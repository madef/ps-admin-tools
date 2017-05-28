<?php

class AT_Employee_Create extends AT_Command_Abstract
{
    public function getCommand()
    {
        return 'employee:create';
    }

    public function getDescription()
    {
        return 'Create employee';
    }

    public function getParams()
    {
        return array(
            array(
                'email',
                'e',
                AT_PARAMS::REQUIRED_PARAM | AT_Params::REQUIRED_VALUE,
                'Email',
            ),
            array(
                'password',
                'p',
                AT_PARAMS::REQUIRED_PARAM | AT_Params::REQUIRED_VALUE,
                'Password',
            ),
            array(
                'firstname',
                'f',
                AT_PARAMS::REQUIRED_PARAM | AT_Params::REQUIRED_VALUE,
                'Firstname',
            ),
            array(
                'lastname',
                'l',
                AT_PARAMS::REQUIRED_PARAM | AT_Params::REQUIRED_VALUE,
                'Lastname',
            ),
            array(
                'profile',
                'P',
                AT_PARAMS::REQUIRED_PARAM | AT_Params::REQUIRED_VALUE,
                'Profile name',
            ),
            array(
                'lang',
                'L',
                AT_PARAMS::OPTIONAL_PARAM | AT_Params::REQUIRED_VALUE,
                'Language iso code',
            ),
        );
    }


    public function run($params)
    {
        if (empty($params->lang)) {
            $lang = Context::getContext()->language->id;
        } else {
            $lang = $this->getLangIdByIso($params->lang);
        }

        if (!$lang) {
            $this->fatal(
                "« {$params->lang} » is not a valid iso code.",
                4
            );
        }

        $profile = $this->getProfileIdByName($params->profile);
        if (!$profile) {
            $this->fatal(
                "« {$params->profile} » is not a valid profile.",
                5
            );
        }

        $employee = new Employee();
        $employee->firstname = $params->firstname;
        $employee->lastname = $params->lastname;
        $employee->passwd = Tools::encrypt($params->password);
        $employee->email = $params->email;
        $employee->id_profile = $profile;
        $employee->id_lang = $lang;
        if (!$employee->save()) {
            $this->fatal(
                "Employee not created.",
                6
            );
        }

        $this->normal($lang);
    }

    protected function getLangIdByIso($isoCode)
    {
        $id_lang = Db::getInstance()->getValue('SELECT `id_lang` FROM `'._DB_PREFIX_.'lang` WHERE `iso_code` = \''.pSQL(strtolower($isoCode)).'\'');

        return $id_lang;
    }

    protected function getProfileIdByName($name)
    {
        $id = Db::getInstance()->getValue('SELECT `id_profile` FROM `'._DB_PREFIX_.'profile_lang` WHERE `name` = \''.pSQL($name).'\'');

        return $id;
    }
}

