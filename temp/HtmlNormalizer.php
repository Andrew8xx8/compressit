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
 * Реализация класса для нормализации HTML кода
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version 1.0.0
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */
class CI_HtmlNormalizer extends CI_Normalizer{
    /**
     * @var string HTML код
     */
	private $htmlCode;

     /**
     * Функция нормализации кода.
     * @param  string $htmlCode HTML Код
     * @return string           HTML Код
     */
    public function normalize($htmlCode){
		$this->htmlCode = $htmlCode;
		
		CI_Log::write("Normalize  HTML", "CI_HtmlNormalizer", CI_Log::INFO, 5);
        $this->htmlCode = $this->removeCrlf($this->htmlCode );
		
        $this->clearFormatting();

        return $this->htmlCode;
    }

    /**
     * Удаляет форматирование.
     * @return void
     */
    public function clearFormatting(){
        $this->htmlCode = preg_replace("#>\s*<#", "><", $this->htmlCode);  	
		$this->htmlCode =	preg_replace("#\"\s*(>|/)#", "\"$1", $this->htmlCode);  	
		
		
        $this->htmlCode = preg_replace('#\\s+(<\\/?(?:area|base(?:font)?|blockquote|body'
            .'|caption|center|cite|col(?:group)?|dd|dir|div|dl|dt|fieldset|form'
            .'|frame(?:set)?|h[1-6]|head|hr|html|legend|li|link|map|menu|meta'			
            .'|ol|opt(?:group|ion)|p|param|t(?:able|body|head|d|h||r|foot|itle)'
            .'|ul' //HTML 5 =)
			.'|acronym|address|article|aside|audio|bdo|big|button|caption|center'
			.'|cite|code|command|datalist|details|dialog|embed|figure|footer'
			.'|header|hgroup|hr|html|mark|nav|section|time|video)\\b[^>]*>)#i', '$1', $this->htmlCode);
					
		$this->htmlCode = preg_replace('#\s*(accesskey|class|contenteditable|contextmenu|dir|hidden'.		
			'|id|lang|spellcheck|style|tabindex|title|href|name|content|xmlns|src|rel|type|media'.	'|onblur|onchange|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup|onload'.
			'|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onreset|onselect|onsubmit|onunload'.
			'|http-equiv|selected|readonly|value)\s*=\s*("|\')(.*?)("|\')#i', ' $1=$2$3$4', $this->htmlCode);
			
        $this->htmlCode = preg_replace('/(<[a-z\\-]+)\\s+([^>]+>)/i', "$1 $2",  $this->htmlCode);
    }		
}
