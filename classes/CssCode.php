<?php 
/**
 * CssCode.php Класс для разбора CSS кода по правилам и последующаяя его оптимизация
 *
 * @author    AndrewKvik
 * @version   0.1a
 * @copyright ChaosLab
 */

require_once("Code.php");
require_once("CssOptimizer.php");
require_once("CssNormalizer.php");

class CI_CssCode extends CI_Code{
    /**
     * без форматирования (CSS одной строкой)
     */
    const NoFormat = 0;

    /*
     * форматирование в столбик (каждое правило представляется в виде отступов)
     */
    const ColFormat = 1;
    
    /**
     * форматирование в строку (одно правило - одна строка)
     */
    const InLineFormat = 2;

    private $urlStore;
    
    var $formattingType = 0;

    var  $errors = '';

    /**
     * @param  $delComments
     * @param  $formattingType
     * @return void
     */
    public function __construct($delComments, $formattingType){
        $this->formattingType = $formattingType;
        $this->delComments = $delComments;
    }

    /**
     * Возвращает тип файла: css, js, html
     * 
     * @return string
     */
    public function getType(){
        return "css";
    }

    /**
     * Функция преобработки
     *
     * @param  string $code
     * @return string
     */
    public function beforeOptimize($cssCode){
        $cssCode= $this->popUrl($cssCode);
        
        return $cssCode;
    }

    /**
     * Функция постобработки
     *
     * @param  string $code
     * @return string
     */
    public function afterOptimize($cssCode){
        $cssCode = $this->pushUrl($cssCode);

        $cssCode = preg_replace("/:\s+/", ":", $cssCode);
        $cssCode = preg_replace("/\s+;\s+/", ";", $cssCode);

        if ($this->formattingType == CI_CssCode::ColFormat)
            $cssCode = $this->formatInCol($cssCode);

        if ($this->formattingType == CI_CssCode::InLineFormat)
             $cssCode = $this->formatInLine($cssCode);

        return $cssCode;
    }

    /**
     * Извлекает все вхождения URL в css файле, включая конструкции data-uri,
     * во временное хранилище
     *
     * @param string $cssCode
     * @return string
     */
    private function popUrl($cssCode){
        preg_match_all("/url\s*\(.*\)/i", $cssCode, $this->urlStore);

        for($i = 0; $i < count($this->urlStore[0]); $i++)
            $cssCode = str_replace($this->urlStore[0][$i], "url(data-url$i)", $cssCode);

        return $cssCode;
    }

    /**
     * Восстанавливает все вхождения URL в css файле, включая конструкции data-uri,
     * из временного хранилища
     *
     * @param string $cssCode
     * @return string
     */
    private function pushUrl($cssCode){
        for($i = 0; $i < count($this->urlStore[0]); $i++)
            $cssCode = preg_replace("/url\(data-url$i\)(;|\n|\s)\s*/", $this->urlStore[0][$i], $cssCode);

        return $cssCode;
    }

    /**
     * Извлекает все комментарии в css файле, включая конструкции data-uri,
     * во временное хранилище
     *
     * @param string $cssCode
     * @return string
     */
    public function popComments($cssCode, &$store){
        /*
         * Комментаррии CSS
         * Находим первое вхождение слеш звёздочка, далее берём любой символ или последовательность
         * слеш звёздочка, пока не встретится последовательность звёздочка слеш. Квантификатор не жадный, по этому
         * матчит ближайшие открывающие и закгрывающие вхождения оставляя внутри открывающие
         */
        return $this->popData("%/\*(.|[^/*])*?\*/\s+%Uis", "commentent", $cssCode, $store);
    }

    /**
     * Восстанавливает все вхождения URL в css файле, включая конструкции data-uri,
     * из временного хранилища
     *
     * @param string $cssCode
     * @return string
     */
    public function pushComments($cssCode, &$store){
        return $this->pushData("commentent", $cssCode, $store);
    }
    
    /**
     * Форматирует css правила располагая их построчно
     * @param string $cssCode
     * @return string
     */
    private function formatInCol($cssCode){
        // Вставляем перевод строки после ; в описании правила
        $cssCode = preg_replace("/@(.*?);\s+/", "@$1;\n", $cssCode);

        // Вставляем перевод строки после ; в описании правила
        $cssCode = preg_replace("/:(.*?);/", ":$1;\n ", $cssCode);

        $cssCode = preg_replace("/{/", "{\n  ", $cssCode);
        if (preg_match("/@media(.*?){\s*/", $cssCode))
            $cssCode = preg_replace("/@media(.*?){\s+/", "@media$1{\n", $cssCode);
        
        $cssCode = preg_replace("/,/", ", ", $cssCode);
        $cssCode = preg_replace("/;?\s*?}/", "\n}\n", $cssCode);
        return $cssCode;
    }

    /**
     * Форматирует css правила располагая их вертикально в виде блоков
     * @param string $cssCode
     * @return string
     */
    private function formatInLine($cssCode){
        $css = preg_replace("/:/", ": ", $cssCode);
        $css = preg_replace("/;/", "; ", $css);
        $css = preg_replace("/\s{0,2}}/", "}\n", $css);
        $css = preg_replace("/,/", ", ", $css);
        return $css;
    }

    /**
     * Возвращает класс - провайер функций для оптимизации кода
     * @abstract
     * @return Optimizer
     */
    public function getOptimizer(){
        return new CI_CssOptimizer();
    }

    /**
     * Возвращает класс - провайер функций для нормализации кода
     * @abstract
     * @return Normalizer
     */
    public function getNormalizer(){
        return new CI_CssNormalizer();
    }
}
?>