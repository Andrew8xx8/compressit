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
 * Абстрактный класс представляющий интерейс для оптимизации кода
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version 1.0.0
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */
abstract class CI_Optimizer {
    /**
     * Функция оптимизации кода
     * @abstract
     * @param  string $code Код
     * @return string       Код
     */
    abstract function optimize($code);
}
?>