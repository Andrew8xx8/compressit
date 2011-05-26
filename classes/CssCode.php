<?php
/**
 * Дата создания: 15.04.11
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version 1.0.0
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */
require_once("Code.php");
require_once("CssOptimizer.php");
require_once("CssNormalizer.php");

/**
 * Реализация абстрактой фабрики {@link CI_Code} для обработки таблиц стилей.
 *
 * @author Andrew Kulakov <avk@8xx8.ru>
 * @version 1.0.0
 * @copyright Andrew Kulakov (c) 2011
 * @package CI
 */
class CI_CssCode extends CI_Code{
    /**
     * @var array Хранилище URL
     */
    private $urlStore = array();

    /**
     * Конструктор класса. Осуществляет инициализацию параметров.
     * @param  boolean $delComments     Удалять ли коментарии?
     * @return void
     */
    public function __construct($delComments){
        $this->delComments = $delComments;       
    }

    /**
     * Возвращает тип файла: css, js, html
     * @abstract
     * @return string тип файла.
     */
    public function getType(){
        return "css";
    }
    
    /**
     * Функция обрабатывающая исходный код перед применением нормализатора и оптимизатора
     * @abstract
     * @param  string $code Код до обработки;
     * @return string       Код после обработки.
     */
    public function beforeOptimize($cssCode){
		CI_Log::write("Before optimize", "CI_CssCode", CI_Log::INFO, 8);
		
        $cssCode = $this->popUrl($cssCode);        		
        return $cssCode;
    }

    /**
     * Функция обрабатывающая оптимизированный код после применения нормализатора и оптимизатора
     * @abstract
     * @param  string $code Код до обработки;
     * @return string       Код после обработки.
     */
    public function afterOptimize($cssCode){
		CI_Log::write("After optimize", "CI_CssCode", CI_Log::INFO, 8);
        $cssCode = $this->pushUrl($cssCode);

        $cssCode = preg_replace("/:\s+/", ":", $cssCode);
        $cssCode = preg_replace("/\s+;\s+/", ";", $cssCode);

        return $cssCode;
    }

    /**
     * Извлекает все вхождения URL в css файле, включая конструкции data-uri,
     * во временное хранилище
     *
     * @param string $cssCode CSS код с URL
     * @return string         CSS код без URL
     */
    private function popUrl($cssCode){
        preg_match_all("/url\s*\(.*\)/i", $cssCode, $this->urlStore);

        for($i = 0; $i < count($this->urlStore[0]); $i++)
            $cssCode = str_replace($this->urlStore[0][$i], "url(data-url$i)", $cssCode);

        return $cssCode;
    }

    /**
     * Восстанавливает все вхождения URL в css файле, включая конструкции data-uri,
     * из временного хранилища
     *
     * @param string $cssCode CSS код без URL
     * @return string         CSS код с URL
     */
    private function pushUrl($cssCode){
        for($i = 0; $i < count($this->urlStore[0]); $i++)
            $cssCode = preg_replace("/url\(data-url$i\)(;|\n|\s)\s*/", $this->urlStore[0][$i], $cssCode);

        return $cssCode;
    }

    /**
     * Извлекает все комментарии в коде
     * во временное хранилище
     *
     * @param string $code   Код с комментариями;
     * @param array  $store  Временное хранилище;
     * @return string        Код без комментариев.
     */
    public function popComments($cssCode, &$store){
		
		// preserve empty comment after '>'
        // http://www.webdevout.net/css-hacks#in_css-selectors
        $cssCode = preg_replace('@>/\\*\\s*\\*/@', '>/*keep*/', $cssCode);
        
        // preserve empty comment between property and value
        // http://css-discuss.incutio.com/?page=BoxModelHack
        $cssCode = preg_replace('@/\\*\\s*\\*/\\s*:@', '/*keep*/:', $cssCode);
        $cssCode = preg_replace('@:\\s*/\\*\\s*\\*/@', ':/*keep*/', $cssCode);
        
        // apply callback to all valid comments (and strip out surrounding ws
        $cssCode = preg_replace_callback('@\\s*/\\*([\\s\\S]*?)\\*/\\s*@'
            ,array($this, 'commentCallBack'), $cssCode);
			
		return $cssCode;
    }

    /**
     * Восстанавливает все комментарии в коде
     * из временного хранилища
     *
     * @param string $code   Код без комментариев;
     * @param array  $store  Временное хранилище;
     * @return string        Код с комментариями.
     */
    public function pushComments($cssCode, &$store){
        return $this->pushData("commentent", $cssCode, $store);
    }
    
    /**
     * Форматирует CSS правила располагая их построчно
     * @param string $cssCode Неформатированный CSS код
     * @return string         Форматированный CSS код
     */
    private function formatInCol($cssCode){
        // Вставляем перевод строки после ; в описании правила
        $cssCode = preg_replace("/@(.*?);\s+/", "@$1;\n", $cssCode);

        // Вставляем перевод строки после ; в описании правила
        $cssCode = preg_replace("/:(.*?);/", ":$1;\n ", $cssCode);

       /* $cssCode = preg_replace("/{/", "{\n  ", $cssCode);
        if (preg_match("/@media(.*?){\s*//*", $cssCode))
            $cssCode = preg_replace("/@media(.*?){\s+/", "@media$1{\n", $cssCode);*/
        
        $cssCode = preg_replace("/,/", ", ", $cssCode);
        $cssCode = preg_replace("/;?\s*?}/", "\n}\n", $cssCode);
        return $cssCode;
    }

    /**
     * Форматирует css правила располагая их вертикально в виде блоков
     * @param  string $cssCode Неформатированный CSS код
     * @return string          Форматированный CSS код
     */
    private function formatInLine($cssCode){
        $css = preg_replace("/:/", ": ", $cssCode);
        $css = preg_replace("/;/", "; ", $css);
        $css = preg_replace("/\s{0,2}}/", "}\n", $css);
        $css = preg_replace("/,/", ", ", $css);
        return $css;
    }

    public function getOptimizer(){
		CI_Log::write("Get delegate class for CSS Optimizer", "CI_CssCode", CI_Log::INFO, 9);
		
        return new CI_CssOptimizer();
    }

    public function getNormalizer(){
		CI_Log::write("Get delegate class for CSS Normalizer", "CI_CssCode", CI_Log::INFO, 9);
		
        return new CI_CssNormalizer();
    }
	
	/**
     * Обрабатывает комментарий и возвращает замену. Учитывает особенности различных браузеров (хаки).
     * @link http://tantek.com/CSS/Examples/midpass.html
     *
     * @param array $m Cовпадения regex
     * 
     * @return string CSS код
     */
    protected function commentCallBack($m)
    {
        $hasSurroundingWs = (trim($m[0]) !== $m[1]);
        $m = $m[1]; 
        // $m is the comment content w/o the surrounding tokens, 
        // but the return value will replace the entire comment.
        if ($m === 'keep') {
            return '/**/';
        }
        if ($m === '" "') {
            // component of http://tantek.com/CSS/Examples/midpass.html
            return '/*" "*/';
        }
        if (preg_match('@";\\}\\s*\\}/\\*\\s+@', $m)) {
            // component of http://tantek.com/CSS/Examples/midpass.html
            return '/*";}}/* */';
        }
        if ($this->_inHack) {
            // inversion: feeding only to one browser
            if (preg_match('@
                    ^/               # comment started like /*/
                    \\s*
                    (\\S[\\s\\S]+?)  # has at least some non-ws content
                    \\s*
                    /\\*             # ends like /*/ or /**/
                @x', $m, $n)) {
                // end hack mode after this comment, but preserve the hack and comment content
                $this->_inHack = false;
                return "/*/{$n[1]}/**/";
            }
        }
        if (substr($m, -1) === '\\') { // comment ends like \*/
            // begin hack mode and preserve hack
            $this->_inHack = true;
            return '/*\\*/';
        }
        if ($m !== '' && $m[0] === '/') { // comment looks like /*/ foo */
            // begin hack mode and preserve hack
            $this->_inHack = true;
            return '/*/*/';
        }
        if ($this->_inHack) {
            // a regular comment ends hack mode but should be preserved
            $this->_inHack = false;
            return '/**/';
        }
        // Issue 107: if there's any surrounding whitespace, it may be important, so 
        // replace the comment with a single space
        return $hasSurroundingWs // remove all other comments
            ? ' '
            : '';
    }
}
?>