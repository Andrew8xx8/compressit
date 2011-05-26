<?php 
/**
 * Дата создания: 15.04.11
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version 1.0.0
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */

require_once("Code.php");
require_once("HtmlOptimizer.php");
require_once("HtmlNormalizer.php");

/**
 * Реализация абстрактой фабрики {@link CI_Code} для обработки HTML кода.
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version 1.0.0
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */
class CI_HtmlCode extends CI_Code{
	/**
     * @var bool Удалить пробельные символы
     */
	private $noSpaces = true;
    /**
     * @var bool Удалить комментарии
     */
	private $noComments = true;  // Если true удаляет html комментарии (<!-- -->)
    /**
     * @var array Хранилище для содержимого элементов  pre
     */
    private $preStore = array();
    /**
     * @var array Хранилище для содержимого тега code
     */
    private $codeStore = array();
    /**
     * @var array Хранилище для содержимого тега textarea
     */
    private $textAreaStore = array();
    
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
        return "html";
    }

    /**
     * Функция обрабатывающая исходный код перед применением нормализатора и оптимизатора
     * @abstract
     * @param  string $htmlCode Код до обработки.
     * @return string           Код после обработки.
     */
    public function beforeOptimize($htmlCode){
        // Извлевкаем всё что внутри тега <pre>
        $htmlCode = $this->popData("%<pre(.*?)>(.*?)</pre>%Uis", "preTag", $htmlCode, $this->preStore);
         // Извлевкаем всё что внутри тега <pre>
        $htmlCode = $this->popData("%<code(.*?)>(.*?)</code>%Uis", "codeTag", $htmlCode, $this->codeStore);
        // Извлевкаем всё что внутри тега <pre>
        $htmlCode = $this->popData("%<textarea(.*?)>(.*?)</textarea>%Uis", "textareaTag", $htmlCode, $this->textAreaStore);

        return $htmlCode;
    }

    /**
     * Функция обрабатывающая оптимизированный код после применения нормализатора и оптимизатора
     * @abstract
     * @param  string $htmlCode Код до обработки.
     * @return string           Код после обработки.
     */
    public function afterOptimize($htmlCode){
        $htmlCode = $this->pushData("preTag", $htmlCode, $this->preStore);      
        $htmlCode = $this->pushData("codeTag", $htmlCode, $this->codeStore);        

        $htmlCode = $this->pushData("textareaTag", $htmlCode, $this->textAreaStore);        
//                                  CI_Log :: write_dump($this->textAreaStore, "HtmlCode :: afterOptimize");  
        return $htmlCode;
    }

    /**
     * Извлекает все комментарии в коде
     * во временное хранилище
     *
     * @param string $htmlCode   Код с комментариями;
     * @param array  $store      Временное хранилище.
     * @return string            Код без комментариев.
     */
    public function popComments($htmlCode, &$store){
        return $this->popData("/<!--([\\s\\S]*?)-->/i", "commentent", $htmlCode, $store);
    }

    /**
     * Извлекает все комментарии в коде
     * во временное хранилище
     *
     * @param string $htmlCode   Код с комментариями;
     * @param array  $store      Временное хранилище.
     * @return string            Код без комментариев.
     */
    public function pushComments($htmlCode, &$store){
        for($i = 0; $i < count($store[0]); $i++)
            // Оставляем комментарий если это условный тег IE
            if (!$this->delComments || preg_match("%(<!--|<!)\[(.*?)\]%", $store[0][$i])) {
                $htmlCode = preg_replace("/-=".md5('commentent')."$i=-/", $store[0][$i]." ", $htmlCode);
            }
            else
                $htmlCode = preg_replace("/-=".md5('commentent')."$i=-/", "", $htmlCode);

        $htmlCode = preg_replace("/\*\/[\s|\n]+/", "*/", $htmlCode);

        return $htmlCode;
    }

     /**
     * Возвращает класс - провайдер функций для оптимизации кода
     * @abstract
     * @return Optimizer
     */
    public function getOptimizer(){
		CI_Log::write("Get delegate class for HTML Optimizer", "CI_HtmlCode", CI_Log::INFO, 9);
		
        return new CI_HtmlOptimizer();
    }

    /**
     * Возвращает класс - провайдер функций для нормализации кода
     * @abstract
     * @return Normalizer
     */
    public function getNormalizer(){
		CI_Log::write("Get delegate class for HTML Normalizer", "CI_HtmlCode", CI_Log::INFO, 9);
		
        return new CI_HtmlNormalizer();
    }
}
?>