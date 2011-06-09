<?php
/**
 * Дата создания: 11.04.11
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */
require_once("Log.php");
/**
 *  Класс осуществляющий опрации записи в файлы.
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version 1.0.0
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */
class CI_FileWriter{
    /**
     * Записывает содержимое $contents в файл
     * @static
     * @param  string $filename Имя файла
     * @param  string $contents Данные
     * @return bool             Результат операции true: запись проведена  успешно, false: произошла ошибка
     */
    public static function write($filename, $contents){

        $file = fopen($filename, "wt");
        if ($file !== false){
            fwrite($file, $contents);
            fclose($file);
            return true;
        } else {
            CI_Log :: write("Ошибка доступа к файлу ".$filename, "FileWriter.php");
            return false;
        }
    }

    /**
     * Сжимаетпо алгоритму GNU ZIP и записывает содержимое $contents в файл
     * @static
     * @param  string $filename Имя файла
     * @param  string $contents Данные
     * @return bool             Результат операции true: запись проведена  успешно, false: произошла ошибка
     */
    public static function writeGZ($filename, $contents){
        $allowGZ = CI::getInstance()->getOption('gzip');
        if ($allowGZ === true) {
            $file = fopen($filename, "wt");

            if ($file !== false){
                fwrite($file, gzcompress ( $contents, 9 ));
                fclose($file);
                return true;
            } else {
                CI_Log :: write("Ошибка доступа к файлу ".$filename, "FileReader.php");
                return false;
            }
        } else
            return false;
    }
}
