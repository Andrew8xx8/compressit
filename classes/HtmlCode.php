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

require_once('JsCode.php');
require_once('CssCode.php');

class CI_HtmlCode extends CI_Code{
	 
	private $noSpaces = true;  // Если true удаляет все лишние пробелы, табуляции и переводы строк
	private $noComments = true;  // Если true удаляет html комментарии (<!-- -->)
    private $preStore = array();
    private $codeStore = array();
    private $textAreaStore = array();

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
        // Извлевкаем всё что внутри тега <pre>
        $htmlCode = $this->popData("%<pre(.*?)>(.*?)</pre>%Uis", "preTag", $htmlCode, $this->preStore);
         // Извлевкаем всё что внутри тега <pre>
        $htmlCode = $this->popData("%<code(.*?)>(.*?)</code>%Uis", "codeTag", $htmlCode, $this->codeStore);
        // Извлевкаем всё что внутри тега <pre>
        $htmlCode = $this->popData("%<textarea(.*?)>(.*?)</textarea>%Uis", "textareaTag", $htmlCode, $this->textAreaStore);

        $htmlCode = $this->optimizeJs($htmlCode);

        $htmlCode = $this->optimizeCss($htmlCode);

        return $htmlCode;
    }

    private function optimizeJs($htmlCode){
        $jsCode = new CI_JsCode(true, true);

        preg_match_all("%<script.*?src=\"(.*?)\".*?>(.*?)</script>%is", $htmlCode, $jsScripts);
        foreach($jsScripts[1] as $script)
            $jsCode->addFile($script);
        $htmlCode = preg_replace("%<script.*?src=\"(.*?)\".*?>(.*?)</script>%is", "", $htmlCode);

        preg_match_all("%<script(.*?)>(.*?)</script>%is", $htmlCode, $jsScripts);
        foreach($jsScripts[2] as $script)
            $jsCode->addCode($script);
        $htmlCode = preg_replace("%<script(.*?)>(.*?)</script>%is", "", $htmlCode);

        $jsCode->optimizeCode();
        $js = $jsCode->sendOptimizedCodeToCache();
        $htmlCode = preg_replace("%(</head>)%is", "<script src=\"$js\"></script>$1",$htmlCode);
        //$js =$jsCode->getOptimizedCode();

        //$this->setCodeToCache($jsCode->getType(), $js);
        
        return $htmlCode;
    }

    private function optimizeCss($htmlCode){
        $cssCode = new CI_CssCode(true, 0);

        preg_match_all("%<style(.*?)>(.*?)</style>%is", $htmlCode, $cssStyles);
        foreach($cssStyles[2] as $style)
           $cssCode->addCode($style);
        $htmlCode = preg_replace("%<style(.*?)>(.*?)</style>%is", "", $htmlCode);

        preg_match_all("%<link.*?(href=\"(.*?)\")?.*?(rel=\"stylesheet\").*?(href=\"(.*?)\").*?>%is", $htmlCode, $cssStyles);
        //foreach($cssStyles[5] as $style)
//            $cssCode->addFile($style);
        $htmlCode = preg_replace("%<link.*?rel=\"stylesheet\".*?>%is", "", $htmlCode);

        $cssCode->optimizeCode();
        $css = $cssCode->sendOptimizedCodeToCache();

        $htmlCode = preg_replace("%(</head>)%is", "<link rel=\"stylesheet\" href=\"$css\" type=\"text/css\" />$1",$htmlCode);

        return $htmlCode;
    }

    /**
     * Функция постобработки
     *
     * @param  string $code
     * @return string
     */
    public function afterOptimize($htmlCode){
        $htmlCode = $this->pushData("preTag", $htmlCode, $this->preStore);      
        $htmlCode = $this->pushData("codeTag", $htmlCode, $this->codeStore);        

        $htmlCode = $this->pushData("textareaTag", $htmlCode, $this->textAreaStore);        
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
    public function popComments($htmlCode, &$store){
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
                $htmlCode = preg_replace("/-=".md5('commentent')."$i=-/", $store[0][$i]." ", $htmlCode);
            }
            else
                $htmlCode = preg_replace("/-=".md5('commentent')."$i=-/", "", $htmlCode);

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