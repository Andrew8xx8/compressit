<?php
/**
 * Cache.php
 * Класс осуществляющий операции кэширования.
 *
 * Дата создания: 09.04.11
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version
 * @copyright Andrew Kulakov (c) 2011
 */
require_once('../CI.php');
require_once('FileReader.php');
require_once('FileWriter.php');

class CI_Cache {
    /**
     * Хранилище исходного кода
     * @var string
     */
    protected $sourceCode = '';

    /**
     * Конструктор. В качестве параметра получает путь к папке с хранилищем кэша
     * @param  $cacheDir string
     */
    public function __construct($cacheDir){
        $this->cacheDir = $cacheDir;
    }

    /**
     * Добавляет код
     * @param string $code
     * @return void
     */
    public function addCode($code){
        $this->sourceCode .= $code;
    }

    /**
     * Добавляет код из файла
     * @param  $filename
     * @return void
     */
    public function addFile($filename){
        $this->sourceCode .= CI_FileReader::read($filename);
    }

    /**
     * Возвращает true если оптимизированного кода из переменной $sourceCode в кэше
     * @return boolean
     */
    protected function isNeedUpdate($fileType){
        return !file_exists($this->getFileName($fileType));
    }

    protected function setCodeToCache($fileType, $code){
        $filename = $this->getFileName($fileType);
        if (!CI_FileWriter::writeGZ($filename, $code))
            CI_FileWriter::write($filename, $code);
        return $this->getWebFileName($fileType);
    }

    /**
     * Возвращает код из кэша либо false в случае если кода в кэше нет
     *
     * @return string || boolean
     */
    protected function getCodeFromCache($fileType){
        if (!$this->isNeedUpdate($fileType))
            return CI_FileReader::read($this->getFileName($fileType));
        else
            return false;
    }

    /**
     * Возвращает возможное имя файла в кеше
     * @return string
     */
    private function getFileName($fileType){
        $cacheDir = CI :: getInstance()->getOption('cache_dir');
        return $cacheDir."/".md5($this->sourceCode).".".$fileType;
    }

    private function getWebFileName($fileType){
        $cacheDir = CI :: getInstance()->getOption('web_cache_dir');
        return $cacheDir."/".md5($this->sourceCode).".".$fileType;
    }
}
