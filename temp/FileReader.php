<?php
/**
 * Дата создания: 09.04.11
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */
require_once("Log.php");
/**
 *  Класс осуществляющий опрации чтения из различных ресурсов.
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version 1.0.0
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */
class CI_FileReader {
    /**
     * Читает файл по адресу $filename.
     * @static
     * @param  string $filename Путь к  файлу
     * @return string|false     Содержимое прочитанного файла | false в случае ошибки
     */
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
            return false;
        }
    }
}
