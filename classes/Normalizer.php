<?php
/**
 * Дата создания: 15.04.11
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version 1.0.0
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */
/**
 * Абстрактный класс представляющий интерейс для нормализации кода
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version 1.0.0
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */
abstract class CI_Normalizer {
    
    /**
     * Заменяет в переданном файле перевод с троки с CRLF(\r\n) на \n
     * @param  string $code
     * @return mixed
     */
    public function removeCrlf($code){
        return str_replace("\r\n", "\n", $code);
    }

    /**
     * Функция нормализации кода
     * @abstract
     * @param  string $code Код
     * @return string       Код
     */
    abstract function normalize($code);
}
