<?php 
/**
 * JSCompress.php Класс для сжатия JavaScript кода по средствам удаления лишних пробелов символов табуляции и переводов строк.
 *
 * @author    Phoenix, AndrewKvik
 * @version   0.2a
 * @copyright ChaosLab
 */

require_once("Code.php");

class CI_JSCompress extends CI_Compressor{
  
	// Тип форматирования выходного JS
  var $formatingType = 0;
  
  var $delComments = true;
  
	public function JSCompress($delComments, $formatingType){
    //$this->sourcePath = $sourcePath;
    $this->formatingType = $formatingType;
    $this->delComments = $delComments;
  }
	
  /* 
    * Генератор констант. Надо, поскольку можно в одном месте этот шаблон 
    * изменить, а в другом забыть.  
    */ 
   private function pattern($i) { 
     return "[{COMPRESIT}={$i}={COMPRESIT}]"; 
   } 
    
  protected function compress($result){  
    // Экранируем спецсимволы, что бы не мешались  
    $result =   htmlentities($result);
    
    // Валидный поиск строк (Идея с хабра)
    // 1) Производим поиск первой одной " или ' (записываем его в \1)
    // 2) Следующий символ " или '?
    //      если нет, тогда следующие два символа равны \" или \'(в зависимости от начала)
    //      если нет, тогда просто пропускаем символ
    // 3) Наткнулись на " – конец.    
    preg_match_all('/(["\'])(\\\\\1|.)*?\1/', $result, $strings);
    
    $ordered = array(); // Массив со строковыми константами. 
    // Заполняем массив строками. Индекс этого массива - номер 
    // шаблона на который была заменена строка в массиве $result  
    for ($i = 0; $i < count($strings[0]); $i++) { 
      $ordered[$i] = $strings[0][$i]; 
      $result = str_replace($strings[0][$i], $this->pattern($i), $result); 
      $i++; 
    }    
    
    // Заменяем коменты на пробел     
    if ($this->delComments)      
      $result = preg_replace("/\/\/.+\n/", "", $result);
    
    // Убиваем форматирование 
    if ($this->formatingType){
      $result = preg_replace("/:\s+/", ":", $result);  
      $result = preg_replace("/(\s+|\n+|\t +)/", " ", $result);      
    }
    
    // Заменяем коменты на пробел      
    if ($this->delComments)
      $result = preg_replace("/\/\*.*?\*\//", "", $result);  
    
    // Убиваем форматирование 
    if ($this->formatingType){
      $result = preg_replace("/\s?{\s?/", "{", $result);    
      $result = preg_replace("/\s?}\s?/", "}", $result);
      $result = preg_replace("/\s?\(\s?/", "(", $result);    
      $result = preg_replace("/\s?\)\s?/", ")", $result);         
      $result = preg_replace("/,\s+/", ",", $result); 
    }
    
    $result = trim(preg_replace("/\s?;\s?/", ";", $result));         
    foreach ($ordered as $i => $string) { 
      // возвращаем строки на место 
      $result = preg_replace($this->pattern($i), "$string", $result); 
    }         
    
    // Экранируем спецсимволы, что бы не мешались  
    $result = html_entity_decode($result);
    
    return $result; 
  }  
}
?>