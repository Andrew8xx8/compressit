<?php
/**
 * Cache.php
 * Базовый класс системы.
 *
 * Дата создания: 10.04.11
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version
 * @copyright Andrew Kulakov (c) 2011
 */
require_once("classes/Log.php");
require_once("classes/HtmlCode.php"); 

class CI {
    private static $instance;
    private $props;

    private function __construct(){
        $this->props = include("config.php");
    }
    
    public static function getInstance(){
        if (empty(self::$instance)) {
            self::$instance = new CI();
        }
        return self::$instance;
    }

    public static function run($buffer){
        CI_Log::write("starting callback function CI::run()", "CI");
        
        $html = new CI_HtmlCode(true, true);

        $html->addCode($buffer);
        CI_Log::write("add Html Code", "CI");

        $html->optimizeCode();

        CI_Log::write("Optimize code", "CI");
        $buffer = $html->getOptimizedCode();

        return $buffer;
    }

    public function start(){
        CI_Log::write("=== Try to running optimizer ===", "CI");
        if (ob_start("CI::run"))
            CI_Log::write("ob_start() success", "CI");
        else
            CI_Log::write("ob_start() fail, check settings", "CI");
    }

    public function end(){
        CI_Log::write("Try to end flush", "CI");
        if (ob_end_flush())
            CI_Log::write("ob_end_flush() success", "CI");
        else
            CI_Log::write("ob_end_flush() fail, check settings", "CI");
    }

    public function setOption($key, $value){
        $this->props[$key] = $value;
    }

    public function getOption($key){
        return $this->props[$key];
    }
}
