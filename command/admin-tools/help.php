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

class AT_Admintools_Help extends AT_Command_Abstract
{
    public function getCommand()
    {
        return 'help';
    }

    public function getDescription()
    {
        return 'Get help for a command';
    }

    public function getParams()
    {
        return array(
            array(
                'command',
                'c',
                AT_Params::REQUIRED_VALUE | AT_Params::REQUIRED_PARAM,
                'Command to explain',
            ),
        );
    }


    public function run($params)
    {
        $commands = json_decode(
            file_get_contents(
                dirname(__FILE__).'/../../cache/commands.json'
            ),
            true
        );

        if (!isset($commands[$params->command])) {
            $this->fatal(
                "Unknow command « {$params->command} ».",
                4
            );
        }

        $this->normal('Command: '.$params->command);
        $this->normal('');
        $usageString = 'php admin-tools/console.php '.$params->command;

        foreach ($commands[$params->command]['params'] as $p) {
            if ($p[2] & AT_Params::REQUIRED_PARAM) {
                $usageString .= " --{$p[0]}";

                if (!($p[2] & AT_Params::NO_VALUE)) {
                    $usageString .= " '{$p[3]}'";
                }
            }
        }

        foreach ($commands[$params->command]['params'] as $p) {
            if (!($p[2] & AT_Params::REQUIRED_PARAM)) {
                $usageString .= " [--{$p[0]}";

                if (!($p[2] & AT_Params::NO_VALUE)) {
                    $usageString .= " '{$p[3]}'";
                }

                $usageString .= "]";
            }
        }

        $this->normal('Usage: '.$usageString);

        $usageString = 'php admin-tools/console.php '.$params->command;
        foreach ($commands[$params->command]['params'] as $p) {
            if ($p[2] & AT_Params::REQUIRED_PARAM) {
                $usageString .= " -{$p[1]}";

                if (!($p[2] & AT_Params::NO_VALUE)) {
                    $usageString .= " '{$p[3]}'";
                }
            }
        }

        foreach ($commands[$params->command]['params'] as $p) {
            if (!($p[2] & AT_Params::REQUIRED_PARAM)) {
                $usageString .= " [-{$p[1]}";

                if (!($p[2] & AT_Params::NO_VALUE)) {
                    $usageString .= " '{$p[3]}'";
                }

                $usageString .= "]";
            }
        }
        $this->normal('Usage: '.$usageString);

        $this->normal('');
        $this->normal('');

        foreach ($commands[$params->command]['params'] as $p) {
            $this->note(sprintf('%-50s', $p[0].': '. $p[3]));
            if ($p[2] & AT_Params::REQUIRED_PARAM) {
                $this->warning('Required attribute');
            }
            if ($p[2] & AT_Params::NO_VALUE) {
                $this->normal('This attribute do not have value');
            } elseif ($p[2] & AT_Params::MULTIPLE_VALUE) {
                $this->normal('Allow multiple value');
            }
            $this->normal('Short tag: '.$p[1]);
            $this->normal('');
        }
    }
}

