<?php
	
	include(__oDIR__ . '/vendor/functions.inc.php');
	include(__oDIR__ . '/vendor/constants.inc.php');
	include(__oCONF__ . '/settings.inc.php');
	
	/*** nullify any existing autoloads ***/
    spl_autoload_register(null, false);

    /*** specify extensions that may be loaded ***/
    spl_autoload_extensions('.class.php, .trait.php');
	
	/*** external class Loader ***/
    function externalClassLoader($class) {
		$class = strtolower($class);
        $filename = $class . '.class.php';
        $file = __oLIB__ . '/external/' . $class . '/' . $filename;
        if (!file_exists($file)) { return false; }
        include $file;
    }
	
	/*** internal class Loader ***/
    function internalClassLoader($class) {
        $filename = strtolower($class) . '.module.class.php';
        $file = __oLIB__ . '/classes/' . $filename;
        if (!file_exists($file)) { return false; }
        include $file;
    }

    function traitLoader($class) {
        $filename = strtolower($class) . '.operations.trait.php';
        $file = __oLIB__ . '/traits/' . $filename;
        if (!file_exists($file)) { return false; }
        include $file;
    }

    /*** register the loader functions ***/
    spl_autoload_register('externalClassLoader');
	spl_autoload_register('internalClassLoader');
    spl_autoload_register('traitLoader');