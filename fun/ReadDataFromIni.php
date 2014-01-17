<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ReadDataFromIni
 *
 * @author Administrator
 */
class ReadDataFromIni {

    var $_settings = array();

    function get($var) {
        $var = explode('.', $var);
        $result = $this->_settings;
        foreach ($var as $key) {
            if (!isset($result [$key])) {
                return false;
            }
            $result = $result [$key];
        }
        return $result;
    }

    function load($file) {
        if (file_exists($file) == false) {
            echo 'can not find file '.$file;
            return false;
        }
        $this->_settings = parse_ini_file($file, true);
    }

}

?>
