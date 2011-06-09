<?php 
/**
 * Дата создания: 09.04.11
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version 1.0.0
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */
require_once("Code.php");
require_once("JsOptimizer.php");
require_once("JsNormalizer.php");
/**
 * Реализация абстрактой фабрики {@link CI_Code} для обработки JavaScript кода.
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version 1.0.0
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */
class CI_JsCode extends CI_Code{
	/**
     * @var bool Удалить пробельные символы
     */
	private $noSpaces = true;
    /**
     * @var bool Удалить комментарии
     */
	private $noComments = true;
     /**
     * @var array Хранилище для содержимого строк
     */
    private $stringStore = array();

    /**
     * Конструктор класса
     * @param  bool $noSpaces    Удалить пробельные символы;
     * @param  bool $noComments  Удалить коментарии.
     */
	public function __construct($noSpaces, $noComments){
        $this->noSpaces = $noSpaces;
        $this->noComments = $noComments;
    }

    /**
     * Возвращает тип файла: css, js, html
     * @abstract
     * @return string тип файла.
     */
    public function getType(){
        return "js";
    }

    /**
     * Функция обрабатывающая исходный код перед применением нормализатора и оптимизатора
     * @abstract
     * @param  string $jsCode Код до обработки.
     * @return string         Код после обработки.
     */
    public function beforeOptimize($jsCode){
        return $jsCode;
    }

    /**
     * Функция обрабатывающая оптимизированный код после применения нормализатора и оптимизатора
     * @abstract
     * @param  string $jsCode Код до обработки.
     * @return string         Код после обработки.
     */
    public function afterOptimize($jsCode){
        return $jsCode;
    }
 
   /**
     * Извлекает все комментарии в коде
     * во временное хранилище
     *
     * @param string $jsCode   Код с комментариями;
     * @param array  $store    Временное хранилище.
     * @return string          Код без комментариев.
     */
    public function popComments($jsCode, &$store){
        return $jsCode;
    }

    /**
     * Извлекает все комментарии в коде
     * во временное хранилище
     *
     * @param string $jsCode   Код с комментариями;
     * @param array  $store    Временное хранилище.
     * @return string          Код без комментариев.
     */
    public function pushComments($jsCode, &$store){
        return $jsCode;            
    }
    
    /**
     * Возвращает класс - провайдер функций для оптимизации кода
     * @abstract
     * @return Optimizer
     */
    public function getOptimizer(){
		CI_Log::write("Get delegate class for JS Optimizer", "CI_JsCode", CI_Log::INFO, 9);
		
        return new CI_JsOptimizer();
    }

    /**
     * Возвращает класс - провайдер функций для нормализации кода
     * @abstract
     * @return Normalizer
     */
    public function getNormalizer(){
		CI_Log::write("Get delegate class for JS Normalizer", "CI_JsCode", CI_Log::INFO, 9);
		
        return new CI_JsNormalizer();
    }
}
?>