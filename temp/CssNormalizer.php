<?php
/**
 * Дата создания: 15.04.11
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version 1.0.0
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */
require_once  ('Normalizer.php');
/**
 * Реализация класса для нормализации CSS кода
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version 1.0.0
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */
class CI_CssNormalizer extends CI_Normalizer{
    /**
     * @var string CSS код
     */
	private $cssCode;

    /**
     * Функция нормализации кода. Нормализация это  удаление пробельных сивмволов, комментариев.
     * @abstract
     * @param  string $cssCode CSS Код
     * @return string          CSS Код
     */
    public function normalize($cssCode){
		CI_Log::write("Normalize  CSS", "CI_CssNormalizer", CI_Log::INFO, 5);
		$this->cssCode = $cssCode;
        $this->cssCode = $this->removeCrlf($this->cssCode);

        $this->normalizeMetrics();
        $this->normalizeColors();
        $this->clearFormatting();
		
        return $this->cssCode;
    }

    /**
     * Убирает в  переданном Css коде все определения типа для ноля,
     * например из 0px => 0
     * @return void
     */
    private function normalizeMetrics(){
        $this->cssCode = preg_replace("/(\s|:)(0em|0ex|0%|0px|0cm|0mm|0in|0pt|0pc)/i", " 0", $this->cssCode);
    }

    /**
     * Приводит шеснадцатиричные записи цветовк сокращённому виду, если это возможно.
     * например из #EEFFDD => #EFD
     * @return void
     */
    private function normalizeColors(){
        $this->cssCode = preg_replace('/([^=])#([a-f\\d])\\2([a-f\\d])\\3([a-f\\d])\\4([\\s;\\}])/i'
            , '$1#$2$3$4$5', $this->cssCode);
    }

    /**
     * Уждаляет из кода все пробельныесимволы
     * @return void
     */
    private function clearFormatting(){
        $this->cssCode = preg_replace("/:\s+/", ":", $this->cssCode);
		$this->cssCode = preg_replace("/;\s+/", ";", $this->cssCode);
        $this->cssCode = preg_replace("/(\s+|\n+|\t+)/", " ", $this->cssCode);
        $this->cssCode = preg_replace("/\s?{\s?/", "{", $this->cssCode);
        $this->cssCode = preg_replace("/\s?;?\s*}\s?/", "}", $this->cssCode);
        $this->cssCode = preg_replace("/,\s+/", ",", $this->cssCode);
        $this->cssCode = preg_replace("/(\s+|\n+|\t+)/", " ", $this->cssCode);
        $this->cssCode = preg_replace("%/(\s+)/%", " ", $this->cssCode);

		$this->cssCode = preg_replace('%@import\\s+url%', '@import url', $this->cssCode);
        $this->cssCode = preg_replace("/@(.*?);\s/", "@$1;", $this->cssCode);
        
		// remove spaces between font families
        $this->cssCode = preg_replace_callback('/font-family:([^;}]+)([;}])/'
            ,array($this, '_fontFamilyCB'), $this->cssCode);
			
        $this->addSpaceAfterPseudoElements($this->cssCode);

    }

    /**
     * Добавляет после всх элементов пробел. Так нужно что бы IE обрабатывал псевдо элементы, иначе обрабатыватьсяне будет.
     * @return void
     */
    private function addSpaceAfterPseudoElements(){
        // Фиксим багу ие шестого c псевдо элементами
        $this->cssCode = preg_replace('/:first-l(etter|ine)\\{/', ':first-l$1 {', $this->cssCode);
        $this->cssCode = preg_replace('/:(active|after|before|first-child|first-letter|first-line|focus|hover|lang|link|visited|)\\{/', ':$1 {', $this->cssCode);
    }
	
	/**
     * Обработка параметра font-family
     * 
     * @param  array $m regex совпадения
     * @return string   
     */
    protected function _fontFamilyCB($m)
    {
        $m[1] = preg_replace('/
                \\s*
                (
                    "[^"]+"      # 1 = family in double qutoes
                    |\'[^\']+\'  # or 1 = family in single quotes
                    |[\\w\\-]+   # or 1 = unquoted family
                )
                \\s*
            /x', '$1', $m[1]);
        return 'font-family:' . $m[1] . $m[2];
    }	
}
