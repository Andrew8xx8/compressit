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

class CI_FileReader {

    public static function read($filename){
        $file = fopen($filename, "rt");
        if ($file !== false){
            $contents = '';
            while (!feof($file)) {
                $contents .= fread($file, 8192);
            }
            fclose($file);
            return $contents;
        } else {
            CI_Log :: write("Ошибка доступа к файлу ".$filename, "FileReader.php");
            die ("Ошибка доступа к файлу ".$filename);
        }
    }
}
