<?php
/**
 * Cache.php
 * Класс осуществляющий опрации чтения из файлов.
 *
 * Дата создания: 09.04.11
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version
 * @copyright Andrew Kulakov (c) 2011
 */
require_once("Log.php");

class CI_FileWriter{

    public static function write($filename, $contents){

        $file = fopen($filename, "wt");
        if ($file !== false){
            fwrite($file, $contents);
            fclose($file);
            return true;
        } else {
            CI_Log :: write("Ошибка доступа к файлу ".$filename, "FileWriter.php");
            die ("Ошибка доступа к файлу ".$filename);
        }
    }

    public static function writeGZ($filename, $contents){ 
        $file = fopen($filename, "wt");
        if ($file !== false){
            fwrite($file, gzcompress ( $contents, 9 ));
            fclose($file);
            return true;
        } else {
            CI_Log :: write("Ошибка доступа к файлу ".$filename, "FileReader.php");
            return false;
        }
    }
}
