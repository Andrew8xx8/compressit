<?php
/**
 * Дата создания: 15.04.11
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version 1.0.0
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */
require_once("Optimizer.php");
/**
 * еализация класса для оптимизации HTML кода
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version 1.0.0
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */
class CI_HtmlOptimizer extends CI_Optimizer{
    /**
     * @var bool Является ли документ XHTML документом
     */
	public $isXhtml;
    /**
     * @var string HTML код
     */
	private $htmlCode;
	
    /**
     * Функция оптимизации кода
     * @param  string $htmlCode Код
     * @return string           Код
     */
    public function optimize($htmlCode){
		$this->htmlCode = $htmlCode;
		
		CI_Log::write("Optimize  HTML", "CI_HtmlOptimizer", CI_Log::INFO, 5);
		$this->isXhtml = (false !== strpos($this->_html, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML'));
		if ($this->isXhtml ||1) {
			$this->addSlashes();
		}

        return $this->htmlCode;
    }

	/**
     * Добавляет завершающий слеш одиночным тегам
     * @return void
     */
	private function addSlashes(){
		$this->htmlCode = preg_replace('#<(img|br|hr|meta|link)(.*?)(>|/>)#i', '<$1$2/>', $this->htmlCode);		
	}
}
