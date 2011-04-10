<?php 
/**
 * HtmlCode.php Класс для сжатия HTML кода по средствам удаления лишних пробелов символов табуляции и переводов строк, а также блоков с комментариями (<!-- -->).
 *
 * @author    AndrewKvik
 * @version   0.1a
 * @copyright ChaosLab
 */

require_once("Code.php");
require_once("HtmlOptimizer.php");
require_once("HtmlNormalizer.php");

class CI_HtmlCode extends CI_Code{
	
	private $noSpaces = true;  // Если true удаляет все лишние пробелы, табуляции и переводы строк
	private $noComments = true;  // Если true удаляет html комментарии (<!-- -->)

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
        return "html";
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
       
        return $htmlCode;
    }

    /**
     * Извлекает все комментарии в css файле, включая конструкции data-uri,
     * во временное хранилище
     *
     * @param string $cssCode
     * @return string
     */
    public function popComments($htmlCode, &$store){
        /*
         * Комментаррии CSS
         * Находим первое вхождение слеш звёздочка, далее берём любой символ или последовательность
         * слеш звёздочка, пока не встретится последовательность звёздочка слеш. Квантификатор не жадный, по этому
         * матчит ближайшие открывающие и закгрывающие вхождения оставляя внутри открывающие
         */
        return $this->popData("/<!--([\\s\\S]*?)-->/i", "commentent", $htmlCode, $store);
    }

    /**
     * Восстанавливает все вхождения URL в css файле, включая конструкции data-uri,
     * из временного хранилища
     *
     * @param string $cssCode
     * @return string
     */
    public function pushComments($htmlCode, &$store){
        for($i = 0; $i < count($store[0]); $i++)
            // Оставляем комментарий если это условный тег IE
            if (!$this->delComments || preg_match("%(<!--|<!)\[(.*?)\]%", $store[0][$i])) {
                $htmlCode = preg_replace("/-=commentent$i=-/", $store[0][$i]." ", $htmlCode);
            }
            else
                $htmlCode = preg_replace("/-=commentent$i=-/", "", $htmlCode);

        $htmlCode = preg_replace("/\*\/[\s|\n]+/", "*/", $htmlCode);

        return $htmlCode;
    }
    
	protected function compress($content){    
		$result = "";
		$f = true;
		$not_pre_tag = true;
		$tags = explode(" ", $content);
		
		foreach($tags as $tag){
			if ($this->noComments && stripos($tag, "<!--") !== FALSE) $f = false;		
			if (stripos($tag, "<pre>") !== FALSE) $not_pre_tag = false;
			if ($f) {
			  if ($not_pre_tag && $this->noSpaces) {
					if($tag != "") {					  
						$result .= trim(preg_replace("/[\s\t\n]+/", " ", $tag));
						
						if (substr($result, strlen($result)-1, 1) != ">")
						  $result .= " ";
					}
				}
				else $result .= " ".$tag;
			}
			if (stripos($tag, "</pre>") !== FALSE) $not_pre_tag = true;		
		  if ($this->noComments && stripos($tag, "-->") !== FALSE) $f = true;
		}    
    $result = preg_replace("/>\s*</", "><", $result); 
    //$result = preg_replace("/\s*\"\s*/", "\"", $result); 		
		return $result;
	}

    /**
     * Возвращает класс - провайер функций для оптимизации кода
     * @abstract
     * @return Optimizer
     */
    public function getOptimizer(){
        return new CI_HtmlOptimizer();
    }

    /**
     * Возвращает класс - провайер функций для нормализации кода
     * @abstract
     * @return Normalizer
     */
    public function getNormalizer(){
        return new CI_HtmlNormalizer();
    }
}
?>