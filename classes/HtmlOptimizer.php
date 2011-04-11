<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Andrew8xx8
 * Date: 10.04.11
 * Time: 23:19
 * To change this template use File | Settings | File Templates.
 */
require_once("Optimizer.php");

class CI_HtmlOptimizer extends CI_Optimizer{

    public function __construct(){

    }
    
    /**
     * @param  $cssCode
     * @return string
     */
    public function optimize($htmlCode){

        return $htmlCode;
    }
}
