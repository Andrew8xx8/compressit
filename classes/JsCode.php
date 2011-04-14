<?php 
/**
 * HtmlCode.php Класс для сжатия HTML кода по средствам удаления лишних пробелов символов табуляции и переводов строк, а также блоков с комментариями (<!-- -->).
 *
 * @author    AndrewKvik
 * @version   0.1a
 * @copyright ChaosLab
 */

require_once("Code.php");
require_once("JsOptimizer.php");
require_once("JsNormalizer.php");

class CI_JsCode extends CI_Code{
	
	private $noSpaces = true;  // Если true удаляет все лишние пробелы, табуляции и переводы строк
	private $noComments = true;  // Если true удаляет html комментарии (<!-- -->)
    private $stringStore = array();

	public function __construct($noSpaces, $noComments){
        $this->noSpaces = $noSpaces;
        $this->noComments = $noComments;
    }

    /**
     * Возвращает тип файла: css, js, html
     *
     * @return string
     */
    public function getType(){
        return "js";
    }

    /**
     * Функция преобработки
     *
     * @param  string $code
     * @return string
     */
    public function beforeOptimize($htmlCode){

        return $htmlCode;
    }

    /**
     * Функция постобработки
     *
     * @param  string $code
     * @return string
     */
    public function afterOptimize($htmlCode){

//                                  CI_Log :: write_dump($this->textAreaStore, "HtmlCode :: afterOptimize");  
        return $htmlCode;
    }
 
    /**
     * Извлекает все комментарии в css файле, включая конструкции data-uri,
     * во временное хранилище
     *
     * @param string $cssCode
     * @return string
     */
    public function popComments($jsCode, &$store){
        return $jsCode;
    }

    /**
     * Восстанавливает все вхождения URL в css файле, включая конструкции data-uri,
     * из временного хранилища
     *
     * @param string $cssCode
     * @return string
     */
    public function pushComments($jsCode, &$store){
        return $jsCode;            
    }
    
    /**
     * Возвращает класс - провайер функций для оптимизации кода
     * @abstract
     * @return Optimizer
     */
    public function getOptimizer(){
        return new CI_JsOptimizer();
    }

    /**
     * Возвращает класс - провайер функций для нормализации кода
     * @abstract
     * @return Normalizer
     */
    public function getNormalizer(){
        return new CI_JsNormalizer();
    }
}
?>