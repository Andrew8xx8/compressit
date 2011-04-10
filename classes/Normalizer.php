<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Andrew8xx8
 * Date: 09.04.11
 * Time: 0:05
 * To change this template use File | Settings | File Templates.
 */
 
abstract class CI_Normalizer {
    /**
     * Заменяет в переданном файле перевод с троки с \r\n на \n
     * @param  $cssCode
     * @return mixed
     */
    public function removeCrlf($cssCode){
        return str_replace("\r\n", "\n", $cssCode);
    }

    /**
     * @abstract
     * @param  $code
     * @return void
     */
    abstract function normalize($code);
}
