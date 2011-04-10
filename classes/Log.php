<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Andrew8xx8
 * Date: 09.04.11
 * Time: 1:39
 * To change this template use File | Settings | File Templates.
 */
define('DEBUG',true);

class CI_Log {
    
    public static function write($message = '', $from=''){
        if (DEBUG == true) {
            if (!file_exists(CI::getInstance()->getOption("logFile")))
                file_put_contents(CI::getInstance()->getOption("logFile"), "");
            $logfile = fopen(CI::getInstance()->getOption("logFile"), "at+");
            fwrite($logfile, date("d.m.Y (h:i:s)", time()).": ".$from." - ".$message. "\n");
            fclose($logfile);
        }
    }
}
