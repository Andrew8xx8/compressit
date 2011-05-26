<?php
/**
 * Дата создания: 10.04.11
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version 1.0.0
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */
require_once("classes/Log.php");
require_once("classes/HtmlCode.php"); 
require_once('classes/JsCode.php');
require_once('classes/CssCode.php');

/**
 * Базовый класс системы. Перехватывает и обрабатывает вывод того скрипта в который внедряется.
 * Является хранилищем глобальных параметров систенмы.
 *
 * Схема работы:
 * Зпуск буферизации вывода функцией ob_start() методом start()
 *  \/
 * Завершение буферизации вывода ob_end_flush() методом end()
 *  \/
 * Обработка результатоф буфера функцией run()
 *
 * Пример внедрения в клиентское приложение.
 * В входном скрипте любого веб-приложения на языке PHP требуется разместить следующие строки.
 * В начале скрипта перед другим кодом:
 *   require_once('../CI.php');
 *   $ci = CI :: getInstance();
 *   $ci->start();
 * В конце скрипта после всех выполненных операций:
 *   $ci->end();
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version 1.0.0
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */
class CI {
    /**
     * @staticvar CI Статический объект типа CI
     */
    private static $instance;

    /**
     * @var array Массив содержащий глобальные параметры системы.
     */
    private $props;

    /**
     * Конструктор класса. Подключает конфигурационный файл тем самым загружая настройки.
     */
    private function __construct(){
        $this->props = include("config.php");
    }

    /**
     * Устанавливает значение произвольного параметра
     * @param  string $key   Имя параметра
     * @param  mixed  $value Значение параметра
     * @return void
     */
    public function setOption($key, $value){
        $this->props[$key] = $value;
    }

    /**
     * Получает значение параметра
     * @param   string $key   Имя параметра
     * @return  mixed         Значение параметра,если параметра нет возвращает false
     */
    public function getOption($key){
        return isset($this->props[$key]) ? $this->props[$key] : false;
    }
    
    /**
     * Возвращает статический объект типа CI, если его не существует то создаёт его.
     * @static
     * @return CI
     */
    public static function getInstance(){		
        if (empty(self::$instance)) {
            self::$instance = new CI();
			CI_Log::write("========= START OPTIMIZER ============", "CI", CI_Log::INFO, 1);
			define("CI_START_TIME", microtime());
        }
        return self::$instance;
    }

    /**
     * Обрабатывает буфер с HTML кодом. После оптимизации возвращает строку с оптимизированными и минимизированным HTML кодом.
     * @static
     * @param  string $buffer буфер с HTML кодом
     * @return string                 HTML код
     */
    public static function run($buffer){
        CI_Log::write("Starting callback function CI::run()", "CI", CI_Log::INFO, 3);
        
        $html = new CI_HtmlCode(true, true);
        $buffer = self :: optimizeJs($buffer);
        $buffer = self :: optimizeCss($buffer);
        $html->addCode($buffer);
		
        CI_Log::write("Add Html Code", "CI", CI_Log::INFO, 3);

        $html->optimizeCode();

        //CI_Log::write("Optimize code", "CI");
        $buffer = $html->getOptimizedCode();

        return $buffer;
    }

    /**
     * Инициализирует процесс буферизации вывода
     * @return void
     */
    public function start(){
        CI_Log::write("Try to running optimizer", "CI", CI_Log::INFO, 3);
        if (ob_start("CI::run"))
            CI_Log::write("ob_start() success", "CI", CI_Log::INFO, 3);
        else
            CI_Log::write("ob_start() fail, check settings", "CI", CI_Log::CI_LOG_ERROR, 0);
    }

    /**
     * Останавливает процесс буферизации вывода и передаёт управление в функцию обратного вызова run()
     * @return void
     */
    public function end(){
        CI_Log::write("Try to end flush", "CI", CI_Log::INFO, 3);
        if (ob_end_flush())
            CI_Log::write("ob_end_flush() success", "CI", CI_Log::INFO, 3);
        else
            CI_Log::write("ob_end_flush() fail, check settings", "CI", CI_Log::CI_LOG_ERROR, 0);
		CI_Log::write("======== TIME LEFT: ".sprintf("%f", (float)(microtime() - START_TIME))." ========", "CI", CI_Log::NOTICE, 0);
		CI_Log::write("========= STOP OPTIMIZER ============", "CI", CI_Log::INFO, 1);		
    }

    /**
     * Обрабатывает сценарии JavaScript
     * @static
     * @param  string $htmlCode HTML код
     * @return string           HTML код
     */
    private static function optimizeJs($htmlCode){
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
			
        return $htmlCode;
    }
    
    /**
     * Обрабатывает сценарии CSS
     * @static
     * @param  string $htmlCode HTML код
     * @return string           HTML код
     */
    private static function optimizeCss($htmlCode){
        $cssCode = new CI_CssCode(true, 0);

        preg_match_all("%<style(.*?)>(.*?)</style>%is", $htmlCode, $cssStyles);
        foreach($cssStyles[2] as $style)
           $cssCode->addCode($style);
        $htmlCode = preg_replace("%<style(.*?)>(.*?)</style>%is", "", $htmlCode);

        preg_match_all("%<link.*?(href.*?=.*?\"(.*?)\")?.*?(rel.*?=.*?\"stylesheet\").*?(href.*?=.*?\"(.*?)\").*?>%is", $htmlCode, $cssStyles);
		CI_Log::write_dump($cssStyles,"CI_Cache", CI_Log::INFO, 9);
		CI_Log::write(count($cssStyles[5]),"CI_Cache", CI_Log::INFO, 9);
        foreach($cssStyles[5] as $style)
            $cssCode->addFile($style);
        $htmlCode = preg_replace("%<link.*?rel=\"stylesheet\".*?>%is", "", $htmlCode);

        $cssCode->optimizeCode();

        $css = $cssCode->sendOptimizedCodeToCache();

        $htmlCode = preg_replace("%(</head>)%is", "<link rel=\"stylesheet\" href=\"$css\" type=\"text/css\" />$1",$htmlCode);

        return $htmlCode;
    }
}
