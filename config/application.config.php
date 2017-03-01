<?php
/**
 * Historia clínica v1.0
 *
 * @link        http://www.medicadiz.com.co
 * @copyright   Copyright (c) 2014 Medicadiz S.A.S.
 * @license     Free to use under the MIT license. (http://www.opensource.org/licenses/mit-license.php)
 * @version     1.0
 *
 * Date:        2014-04-25
 * Autor:       Darío Rivera
 */

return array(
    'modules' => array(
        'Application',
        'Auth',
        'Settings',
        'Admissions',
        'MedicalHistory',
        ),
    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor'
            ),
        'config_glob_paths' => array('config/autoload/{,*.}{global,local}.php')
        )
    );
