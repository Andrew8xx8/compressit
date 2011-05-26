<?php
/**
 * Дата создания: 09.04.11
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */
require_once('../CI.php');
require_once('FileReader.php');
require_once('FileWriter.php');
/**
 * Класс для работы с кешированием.
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version 1.0.0
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */
class CI_Cache {
    /**
     * @var string Хранилище исходного кода
     */
    protected $sourceCode = '';

    /**
     * Конструктор.
     * @param string $cacheDir путь к папке с хранилищем кэша
     */
    public function __construct($cacheDir){
        $this->cacheDir = $cacheDir;
    }

    /**
     * Добавляет код
     * @param string $code Код
     * @return void      
     */
    public function addCode($code){
        $this->sourceCode .= $code;
		CI_Log::write("Added code size: ".sizeof($this->sourceCode),"CI_Cache", CI_Log::INFO, 9);
    }

    /**
     * Добавляет код из файла
     * @param  string $filename  Путь к файлу
     * @return void
     */
    public function addFile($filename){
        $this->sourceCode .= CI_FileReader::read($filename);
		CI_Log::write("Added file ".$filename." size: ".sizeof($this->sourceCode),"CI_Cache", CI_Log::INFO, 9);
    }

    /**
     * Возвращает true если оптимизированного кода из переменной $sourceCode в кэше
     * @return boolean Состояние файла в кеше
     */
    protected function isNeedUpdate($fileType){	
		CI_Log::write("Need update ".file_exists($this->getFileName($fileType))." ".$this->getFileName($fileType)." ".$fileType , "CI_Cache", CI_Log::INFO, 9);
		//development mode
        return true;
        //return !file_exists($this->getFileName($fileType));
    }

    /**
     * Поместить  код во временное хранилище
     * @param  string $fileType Тип файла
     * @param  string $code     Код
     * @return string
     */
    protected function setCodeToCache($fileType, $code){
        $filename = $this->getFileName($fileType);
        if (!CI_FileWriter::writeGZ($filename, $code))
            CI_FileWriter::write($filename, $code);		
        return $this->getWebFileName($fileType);
    }

    /**
     * Возвращает код из кэша
     * @param  string $fileType Тип файла
     * @return string
     */
    protected function getCodeFromCache($fileType){  
		return CI_FileReader::read($this->getFileName($fileType));
    }

    /**
     * Возвращает возможное полный путь к файлу в кеше
     * @param  string $fileType Тип файла
     * @return string           Путь к файлу
     */
    private function getFileName($fileType){
        $cacheDir = CI :: getInstance()->getOption('cache_dir');
        return $cacheDir.CI :: getInstance()->getOption('folder_delimiter').md5($this->sourceCode).".".$fileType;
    }

	/**
     * Возвращает возможное полный HTTP путь к файлу в кеше
     * @param  string $fileType Тип файла
     * @return string           Путь к файлу
     */
    private function getWebFileName($fileType){
        $cacheDir = CI :: getInstance()->getOption('web_cache_dir');
        return $cacheDir."/".md5($this->sourceCode).".".$fileType;
    }
}
