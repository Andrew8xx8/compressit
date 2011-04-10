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
require_once('FileReader.php');

class CI_Cache {
    /**
     * Путь к папке с хранилищем кэша
     * @var string
     */
    private $cacheDir;

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

    /**
     * Возвращает код из кэша либо false в случае если кода в кэше нет
     *
     * @return string || boolean
     */
    protected function getCodeFromCache($fileType){
        if (!isNeedUpdate($fileType))
            return CI_FileReader::read($this->getFileName($fileType));
        else
            return false;
    }

    /**
     * Возвращает возможное имя файла в кеше
     * @return string
     */
    private function getFileName($fileType){
        return $this->cacheDir."/".md5($this->sourceCode).".".$fileType;
    }

}
