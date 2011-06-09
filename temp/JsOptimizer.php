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
 * Реализация класса для оптимизации JavaScript кода
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version 1.0.0
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */
class CI_JsOptimizer extends CI_Optimizer{
    /**
     * @var string JavaScript код
     */
	private $jsCode;
    
    /**
     * Функция оптимизации кода
     * @param  string $jsCode Код
     * @return string         Код
     */
    public function optimize($jsCode){
		CI_Log::write("Optimize JS", "CI_JsOptimizer", CI_Log::INFO, 8);
		
        return $jsCode;
    }
}
