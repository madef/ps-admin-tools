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

class AT_Params
{
    const NO_VALUE = 0x0000001;
    const REQUIRED_VALUE = 0x000010;
    const OPTIONAL_VALUE = 0x000100;
    const MULTIPLE_VALUE = 0x001000;
    const REQUIRED_PARAM = 0x010000;
    const OPTIONAL_PARAM = 0x100000;

    private static $instance = null;
    private $command = null;
    private $values = array();

    private function __construct($argv)
    {
        if (!is_null($argv)) {
            $this->values = $argv;
        }

        // The first argument is the console.php
        array_shift($this->values);

        if (!empty($this->values)) {
            $this->command = array_shift($this->values);
        }
    }

    public static function setInstance($argv)
    {
        self::$instance = new self($argv);
    }

    public static function getInstance()
    {
        return self::$instance;
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function getValues($params)
    {
        /*
        $short = array();
        $long = array();
        foreach ($params as $param) {
            $p = $param[0];
            if (!empty($param[0])) {
                if ($param[2] & self::NO_VALUE) {
                    $p .= '';
                } elseif ($param[2] & self::OPTIONAL_VALUE) {
                    $p .= '::';
                }  else {
                    $p .= ':';
                }
            }
            $short[] = $p;

            $p = $param[1];
            if (!empty($param[1])) {
                if ($param[2] & self::NO_VALUE) {
                    $p .= '';
                } elseif ($param[2] & self::OPTIONAL_VALUE) {
                    $p .= '::';
                }  else {
                    $p .= ':';
                }
            }
            $long[] = $p;
        }
        var_export($short);
        var_export($long);
        $result = getopt(implode($short), $long);
        var_export($result);
         */

        // explode all values
        $values = array();

        foreach ($this->values as $value) {
            $res = preg_match('/^(-\w)=(.*)$/', $value, $match);
            if ($res) {
                $values[] = $match[1];
                $values[] = $match[2];
                continue;
            }
            $res = preg_match('/^(-\w)([^=].*)$/', $value, $match);
            if ($res) {
                $values[] = $match[1];
                $values[] = $match[2];
                continue;
            }
            $res = preg_match('/^(--\w+)=(.*)$/', $value, $match);
            if ($res) {
                $values[] = $match[1];
                $values[] = $match[2];
                continue;
            }
            $values[] = $value;
        }

        // merge value and param
        $merged = array();
        $currentKey = null;
        foreach ($values as $value) {
            if (strpos($value, '-') === 0) {
                $currentKey = null;
                foreach ($params as $p) {
                    if ("-{$p[1]}" === $value) {
                        $currentKey = $p[0];
                        $currentParam = $p;
                    } elseif ("--{$p[0]}" === $value) {
                        $currentKey = $p[0];
                        $currentParam = $p;
                    }
                }
                if (is_null($currentKey)) {
                    throw new Exception("Invalid parameter « {$value} »");
                }

                if (!isset($merged[$currentKey])) {
                    $merged[$currentKey] = null;
                }
                continue;
            }

            if (empty($currentKey)) {
                throw new Exception("Invalid parameter « {$value} »");
            }

            if (is_string($merged[$currentKey])) {
                $merged[$currentKey] = array($merged[$currentKey], $value);
            } elseif (is_array($merged[$currentKey])) {
                $merged[$currentKey][] = $value;
            } else {
                $merged[$currentKey] = $value;
            }

            $currentKey = null;
        }

        // Validate parameters
        foreach ($params as $p) {
            if (!array_key_exists($p[0], $merged)) {
                if ($p[2] & self::REQUIRED_PARAM) {
                    throw new Exception("Parameter « {$p[0]} » is required.");
                }
                continue;
            }

            if ($p[2] & self::NO_VALUE && !is_null($merged[$p[0]])) {
                throw new Exception("Parameter « {$p[0]} » do not require a value.");
            } elseif ($p[2] & self::REQUIRED_VALUE && is_null($merged[$p[0]])) {
                throw new Exception("Parameter « {$p[0]} » require a value.");
            } elseif (!($p[2] & self::MULTIPLE_VALUE) && is_array($merged[$p[0]])) {
                throw new Exception("Parameter « {$p[0]} » do not allow multiple value.");
            }
            if (!is_array($merged[$p[0]]) && ($p[2] & self::MULTIPLE_VALUE)) {
                $merged[$p[0]] = array($merged[$p[0]]);
            }
        }

        return (object) $merged;
    }
}

