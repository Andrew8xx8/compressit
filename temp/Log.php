<?php
/**
 * Класс предназначен для журналирования сообщений генериуемых системой.
 *
 * <p>Для нормальной работы в кофигурационном файле должны быть обязательно определены следующие параметры:</p>
 * <ul>
 *  <li>'logFile' => 'путь_к_файлу_журнала.txt';</li>
 *	<li>'logLevel' => число от -1 до 10 определяющее уровень журналировая.</li>
 * </ul>
 * <p>Уровень журналирования определяет то насколько подробным должен быть журнал работы системы. При -1журналирование отключено, при 10 в журнал записываются абсолютно все сообщения генерируемые системой.</p>
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version 1.0.0
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */

class CI_Log {
    /**
     * Тип сообщения. Сообщение об  ошибке.
     */
    const ERROR = 2;
    
    /**
     * Тип сообщения. Предупреждение.
     */
	const NOTICE = 1;

    /**
     * Тип сообщения. Информационное сообщение.
     */
	const INFO = 0;		

    /**
     * Добавляет запись в лог файл журнала. В качестве сообщения принимает строку.
     * @static
     * @param string $message Сообщение;
     * @param string $from    Название класса в котором сгенерированно сообщение;
     * @param int    $type    Тип сообщения, определяемый константами данного класса;
     * @param int    $level   Уровень журналирования при котором сообщение отображается. Число от 0 до 10.
     * @return void
     */
    public static function write($message = '', $from='', $type = CI_Log::INFO, $level = 0){		
        if (CI::getInstance()->getOption("logLevel") > $level) {            			
            if (!file_exists(CI::getInstance()->getOption("logFile")))
                file_put_contents(CI::getInstance()->getOption("logFile"), "");
            $logfile = fopen(CI::getInstance()->getOption("logFile"), "at+");
            fwrite($logfile, CI_Log::typeToText($type).": ".date("d.m.Y (h:i:s)", time()).": ".$from." - ".$message. "\n");
            fclose($logfile);
        }
    }                          

    /**
     * Осущетсвляет вывод произвольных данных в файл журнала. В качестве сообщения принимает любую структуру.
     * @static
     * @param mixedd $object  Любая структура данных предназначенная для записи в журнал;
     * @param string $from    Название класса в котором сгенерированно сообщение;
     * @param int    $type    Тип сообщения, определяемый константами данного класса;
     * @param int    $level   Уровень журналирования при котором сообщение отображается. Число от 0 до 10.
     * @return void
     */
    public static function write_dump($object, $from='', $type = CI_Log::INFO, $level = 0){
        if (CI::getInstance()->getOption("logLevel") > $level) {            
            self :: write(var_export($object), $from, $type, $level);
        }
    }

	/**
     * Возвращает текстовое представление константы типа сообщения.
     * @static
     * @param  $type  Константа класса CI_Log
     * @return string Текстовое предствление типа
     */
	private static function typeToText($type){
		switch ($type){
			case CI_Log::ERROR:
				return "ERROR";
				break;
			case CI_Log::INFO:
				return "INFO";
				break;
			case CI_Log::ERROR:
				return "NOTICE";
				break;
		}
		return "INFO";
	}
}
