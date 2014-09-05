<?php

namespace ForkCMS\Bundle\InstallerBundle\Service;

/**
 * This service installs fork
 *
 * @author Davy Hellemans <davy@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Dieter Vanden Eynde <dieter@netlash.com>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 */
class ForkInstaller
{
    /**
     * Installs Fork
     *
     * @param  array  $data The collected data required for Fork
     * @return bool         Is Fork successfully installed?
     */
    public function install(array $data)
    {
        if (!$this->isValidData($data)) {
            return false;
        }

        // extend execution limit
        set_time_limit(0);
        ini_set('memory_limit', '16M');

        var_dump('ok');exit;
    }

    /**
     * Checks if our given data is complete and valid
     *
     * @param  array $data The collected data required to install Fork
     * @return bool        Do we have all the needed data
     */
    protected function isValidData(array $data)
    {
        if (
            !in_array('db_hostname', $data)
            || !in_array('db_username', $data)
            || !in_array('db_password', $data)
            || !in_array('db_database', $data)
            || !in_array('db_port', $data)

            || !in_array('languages', $data)
            || !in_array('interface_languages', $data)
            || !in_array('default_language', $data)
            || !in_array('default_interface_language', $data)

            || !in_array('modules', $data)
            || !in_array('example_data', $data)

            || !in_array('email', $data)
            || !in_array('password', $data)
        ) {
            return false;
        }

        return true;
    }
}
