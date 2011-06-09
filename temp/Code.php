<?php
/**
 * Дата создания: 09.04.11
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version 1.0.0
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */
require_once('Cache.php');
/**
 * Абстрактный класс для обработки кода. Содержит общую логику рпоцесса оптимизации для всех типов кода.
 * 
 * <p>Расширяет класс кэша используя его функционал для хранения различных кусков кода.</p>
 * <p>Является абстрактной фабрикой.</p>
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version 1.0.0
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */
abstract class CI_Code extends CI_Cache{
    /**
     * @var string Строка с кодом прошедшим оптимизацию
     */
    protected $optimizedCode = "";
    /**
     * @var int Размер кода до оптимизации
     */
    protected $sizeBefore = 0;
    /**
     * @var int Размер кода после оптимизации
     */
    protected $sizeAfter = 0;
    /**
     * @var array Массив хранящий коментарии встречающиеся в обрабатываемом коде
     */
    protected $commentStore = Array();
    /**
     * @var bool Удалять ли комметы?
     */
    protected $delComments = true;
    
    /**
     * Реализует общую лугику обработки любого кода системой.
     * <p>Определяет размер кода до обработки, записывает его в {@link $sizeBefore}. Если код ранее уже  был обработан, то возвращает результаты из кеша, если нет, то запускует процесс оптимизации.</p>
     * <p>После получения результата оптимизации записывает код его в {@link $optimizedCode}, определяет размер после обработки и записывает его в {@link s$sizeAfter}.</p>
     * <p>Делает пометку в журнал файле о статистике оптимизации.</p>
     * @return void
     */
    public function optimizeCode() {
		CI_Log::write("Try to optimize code of type '".$this->getType()."'", "CI_Code", CI_Log::INFO, 1);
		
        $this->sizeBefore = strlen($this->sourceCode);
		
        if ($this->isNeedUpdate($this->getType())){
            $this->optimizedCode = $this->processCode();
        } else {
            $this->optimizedCode = $this->getCodeFromCache($this->getType());
			CI_Log::write("Reading from cache file:'"."'", "CI_Code", CI_Log::INFO, 1);
		}
        
        $this->sizeAfter = strlen($this->optimizedCode);
		CI_Log::write("Compression of ".$this->getType().". Before ".$this->sizeBefore." chars, after ".$this->sizeAfter. ", ".$this->GetPercent()."%", "CI_Code", CI_Log::INFO, 1);
    }
    
    /**
     * Определяет непесредственно алгоритм оптимизации кода.
     * <p>Извлекает коментарии из кода ({@link popComments()}), выполняет функцию
     * преобработки 9@link beforeOptimize()}), применяет нормализатор, применяет оптимизатор, возвращает
     * комментарии на место ({@link pushComments()}), выполняет функцию пост-оюбработки ({@link afterOptimize()})</p>
     * <p>Для выполнения специфичных функций для различных типов кода использует классы-делегаты.</p>
     * <p>Подробнее о делегатах см. {@link CI_Normalizer} и {@link CI_Optimizer}.</p>
     * @final
     * @return string Оптимизированный код.
     */
    private final function processCode() {
        // Инициализируем классы-делегаты
        $normalizer = $this->getNormalizer();
        $optimizer = $this->getOptimizer();

        $code = $this->sourceCode;

        // Извлекаем комментарии
        $code = $this->popComments($code, $this->commentStore);

        $code = $this->beforeOptimize($code);

        $code = $normalizer->normalize($code);
        $code = $optimizer->optimize($code);

        // Возвращаем коментарии на место
        $code = $this->pushComments($code, $this->commentStore);

        $code = $this->afterOptimize($code);
        return $code;
    }

    /**
     * Возвращает оптимизированный код
     * @return string оптимизированный код.
     */
    public function getOptimizedCode() {
       return $this->optimizedCode;
    }

    /**
     * Выводит оптимизируемый код
     * @return void
     */
	public function displayOptimizedCode(){
		echo $this->optimizedCode;
	}

    /**
     * Возвращает размер кода в символах до обработки
     * @return int размер кода в символах.
     */
    public function getSizeBefore(){
        return $this->sizeBefore;
    }

    /**
     * Возвращает размер кода в символах после обработки
     * @return int размер кода в символах.
     */
    public function getSizeAfter(){
        return $this->sizeAfter;
    }

    /**
     * Возвращает процент сжатия
     * @return int процент сжатия.
     */
    public function getPercent(){
        if ($this->sizeBefore == 0) {
            return 0;
        }
        return (100 - (int)(($this->sizeAfter/$this->sizeBefore)*100));
    }

    /**
     * Отправляет код во временное хранилище.
     * @return string HTTP путь до файла во временном хранилище.
     */
    public function sendOptimizedCodeToCache(){		
		return $this->setCodeToCache($this->getType(), $this->getOptimizedCode());
    }

    /**
     * Заменяет все все вхождения регулярного выражения $popPattern на "-+=".$pushPattern.Счётчик."=+-"
     * сохраняя заменённые данные в массив $store.
     *
     * @param  string $popPattern   Regexp-шаблон для определения контента;
     * @param  string $pushPattern  Шаблон для замены;
     * @param  string $code string  Код;
     * @param  array  $store        Хранилище с извлечёнными даннми;
     * @return string               Код после извлечения данных.
     */
    public function popData($popPattern, $pushPattern, $code, &$store){

        preg_match_all($popPattern, $code, $store);

        for($i = 0; $i < count($store[0]); $i++)
            $code = str_replace($store[0][$i], "-=".md5($pushPattern)."$i=-", $code);

        return $code;
    }
    
    /**
     * Заменяет все все вхождения "-+=".$pushPattern.Счётчик."=+-" на соответствующую счётчику запись в хранилище $store.
     *
     * @param  string $pushPattern  Шаблон для определения контента;
     * @param  string $code string  Код;
     * @param  array  $store        Хранилище с извлечёнными даннми;
     * @return string               Код после помещения данных данных.
     */
    public function pushData($pushPattern, $code, &$store){
        for($i = 0; $i < count($store[0]); $i++)
            if (!$this->delComments || $pushPattern != "comments" ) {
                $code = preg_replace("/-=".md5($pushPattern)."$i=-/", $store[0][$i]." ", $code);
            }
            else
                $code = preg_replace("/-=".md5($pushPattern)."$i=-/", "", $code);

        $code = preg_replace("/\*\/[\s|\n]+/", "*/", $code);

        return $code;
    }

    /**
     * Возвращает класс - провайдер функций для оптимизации кода
     * @abstract
     * @return Optimizer
     */
    abstract function getOptimizer();

    /**
     * Возвращает класс - провайдер функций для нормализации кода
     * @abstract
     * @return Normalizer
     */
    abstract function getNormalizer();

    /**
     * Функция обрабатывающая исходный код перед применением нормализатора и оптимизатора
     * @abstract
     * @param  string $code Код до обработки;
     * @return string       Код после обработки.
     */
    abstract function beforeOptimize($code);

    /**
     * Функция обрабатывающая оптимизированный код после применения нормализатора и оптимизатора
     * @abstract
     * @param  string $code Код до обработки;
     * @return string       Код после обработки.
     */
    abstract function afterOptimize($code);
    
    /**
     * Возвращает тип файла: css, js, html
     * @abstract
     * @return string тип файла.
     */
    abstract function getType();

    /**
     * Извлекает все комментарии в коде
     * во временное хранилище
     *
     * @param string $code   Код с комментариями;
     * @param array  $store  Временное хранилище;
     * @return string        Код без комментариев.
     */
    abstract function popComments($code, &$store);
/**
     * Извлекает все комментарии в коде
     * во временное хранилище
     *
     * @param string $code   Код с комментариями;
     * @param array  $store  Временное хранилище;
     * @return string        Код без комментариев.
     */
    abstract function pushComments($code, &$store);
}
?>