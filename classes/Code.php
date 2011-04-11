<?php 
/** 
 * Code.php
 * Абстрактный класс для обработки кода. Содержит общую логику рпоцесса оптимизации для всех типов кода.
 * Расширяет класс кэша используя его функционал для хранения различных кусков кода.
 * Абстрактная фабрика.    
 *
 * Дата создания: 09.04.11
 * 
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version
 * @copyright Andrew Kulakov (c) 2011
 */
require_once('Cache.php');

abstract class CI_Code extends CI_Cache{
    /**
     * @var string
     */
    protected $optimizedCode = '';
    protected $sizeBefore;
    protected $sizeAfter;
    protected $commentStore;
    protected $delComments = true;
    
    /**
     * @return void
     */
    public function optimizeCode() {
        $this->sizeBefore = strlen($this->sourceCode);

        if ($this->isNeedUpdate($this->getType())){
            // Инициализируем классы-делегаты
            $normalizer = $this->getNormalizer();
            $optimizer = $this->getOptimizer();

            $code = $this->sourceCode;

            // Извлекаем комментарии
            $code = $this->popComments($code, $this->commentStore);

            $code = $this->beforeOptimize($code);
            
            $code = $normalizer->normalize($code);
            $code = $optimizer->optimize($code);

            // Возвращаем коментарии на место
            $code = $this->pushComments($code, $this->commentStore);

            $code = $this->afterOptimize($code);
        } else
            $code = $this->getCodeFromCahce($this->getType());

        $this->optimizedCode = $code;
        
        $this->sizeAfter = strlen($this->optimizedCode);
    }
    
    /**
     * Возвращает оптимизированный код
     * @param $code string
     * @return string
     */
    public function getOptimizedCode() {
       return $this->optimizedCode;
    }

    /**
     * Выводит оптимизируемый код
     * @param $code string
     * @return void
     */
	public function displayOptimizedCode($code){
		echo $this->optimizedCode;
	}

    /**
     * Читает, оптимизирует и сохраняет файл
     * Возвращает false в случае ошибки доступа к файлу.
     * @param  string $sourcePath
     * @param  string $destinationPath
     * @return bool
     */
	public function compress_file($sourcePath, $destinationPath){
        if (file_exists($sourcePath))
            $file = file_get_contents($sourcePath);
        else
            return false;
        
		$f = fopen($destinationPath, "w+");
        fwrite($f, $this->fetch($file));
        fclose($f);
        
		return true;
	}

    /**
     * Возвращает размер кода в символах до обработки
     * @return int
     */
    public function getSizeBefore(){
        return $this->sizeBefore;
    }

    /**
     * Возвращает размер кода в символах после обработки
     * @return int
     */
    public function getSizeAfter(){
        return $this->sizeAfter;
    }

    /**
     * Возвращает процент сжатия
     * @return int
     */
    public function getPercent(){
        return ((int)(($this->sizeAfter/$this->sizeBefore)*100));
    }

    /**
     * Заменяет все все вхеждения регулярного выражения $popPattern на "-+=".$pushPattern.Счётчик."=+-"
     * сохраняя заменённые данные в массив $store.
     *
     * @param  $popPattern string
     * @param  $pushPattern string
     * @param  $code string
     * @param  $store array
     * @return string
     */
    public function popData($popPattern, $pushPattern, $code, &$store){

        preg_match_all($popPattern, $code, $store);

        for($i = 0; $i < count($store[0]); $i++)
            $code = str_replace($store[0][$i], "-=".md5($pushPattern)."$i=-", $code);

        return $code;
    }

    public function pushData($pushPattern, $code, &$store){
        for($i = 0; $i < count($store[0]); $i++)
            if (!$this->delComments) {
                $code = preg_replace("/-=".md5($pushPattern)."$i=-/", $store[0][$i]." ", $code);
            }
            else
                $code = preg_replace("/-=".md5($pushPattern)."$i=-/", "", $code);

        $code = preg_replace("/\*\/[\s|\n]+/", "*/", $code);

        return $code;
    }

    /**
     * Возвращает класс - провайер функций для оптимизации кода
     * @abstract
     * @return Optimizer
     */
    abstract function getOptimizer();

    /**
     * Возвращает класс - провайер функций для нормализации кода
     * @abstract
     * @return Normalizer
     */
    abstract function getNormalizer();

    /**
     * Функция обрабатывающая исходный код перед оптимизацией
     * @abstract
     * @param  string $code
     * @return string
     */
    abstract function beforeOptimize($code);

    /**
     * Функция обрабатывающая оптимизированный код после оптимизации
     * @abstract
     * @param  string $code
     * @return string
     */
    abstract function afterOptimize($code);
    
    /**
     * Возвращает тип файла: css, js, html
     * @abstract
     * @return string
     */
    abstract function getType();

    /**
     * Извлекает все комментарии в коде
     * во временное хранилище
     *
     * @param string $code
     * @param array $store
     * @return string
     */
    abstract function popComments($code, &$store);

    /**
     * Восстанавливает все комментарии в коде
     * из временного хранилища
     *
     * @param string $code
     * @param array $store
     * @return string
     */
    abstract function pushComments($code, &$store);
}
?>