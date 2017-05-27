# ps-admin-tools

## Installation
This project must be cloned on the root of your PrestaShop.

 git clone git@github.com:madef/ps-admin-tools.git admin-tools

## Usage

List all available commands:
 php admin-tools/console.php

Install a module
 php admin-tools/console.php module:install -m <module name>

Get some help about a command
 php admin-tools/console.php help -c module:install

## Create your own command

The command are php classes put on a subfolder of command directory.
For this exemple we will create a command name company:hello that display hello or a given parameter.

Here is the source of the file command/company/hello.php:

```
<?php

class AT_Company_Hello extends AT_Command_Abstract
{
    public function getCommand()
    {
        return 'company:hello';
    }

    public function getDescription()
    {
        return 'Say hello';
    }

    public function getParams()
    {
        return array(
            array(
                'string',
                's',
                AT_PARAMS::OPTIONAL_PARAM | AT_Params::REQUIRED_VALUE,
                'String to display instead of hello',
            ),
        );
    }


    public function run($params)
    {
        $string = 'Hello!';
        if (isset($params->string)) {
            $string = $params->string;
        }

        $this->normal($string);
    }
}

```

The parameters can be optional or required (AT_Params::OPTIONAL_PARAM, AT_Params::REQUIRED_PARAM).
The parametters can have no value (AT_Params::NO_VALUE), an optional value (AT_Params::OPTIONAL_VALUE) or a required value (AT_Params::REQUIRED_VALUE).
The parametters can be set multiple time (AT_Params::MULTIPLE_VALUE).

The command is not usable, the cache need to be flushed:
 php admin-tools/console.php admin-tools:cache


Now you can try your command:
 php admin-tools/console.php company:hello
 php admin-tools/console.php company:hello -s 'An other message'
