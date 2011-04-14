<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Andrew8xx8
 * Date: 10.04.11
 * Time: 23:19
 * To change this template use File | Settings | File Templates.
 */
require_once ('Normalizer.php');

class CI_JsNormalizer extends CI_Normalizer{
    private function pattern($i) { 
        return "[{COMPRESIT}={$i}={COMPRESIT}]"; 
    } 
 
    public function normalize($jsCode){
 /*       $jsCode = $this->removeCrlf($jsCode);

        // Экранируем спецсимволы, что бы не мешались  
        $jsCode =   htmlentities($jsCode);
        
        // Валидный поиск строк (Идея с хабра)
        // 1) Производим поиск первой одной " или ' (записываем его в \1)
        // 2) Следующий символ " или '?
        //      если нет, тогда следующие два символа равны \" или \'(в зависимости от начала)
        //      если нет, тогда просто пропускаем символ
        // 3) Наткнулись на " – конец.    
//        preg_match_all('%(\")(\\\1|.)*?\1%mis', $jsCode, $strings);
        preg_match_all('%".*?"%', $jsCode, $strings); 
        print_r($strings);       
print_r($jsCode); die;        
        $ordered = array(); // Массив со строковыми константами. 
        // Заполняем массив строками. Индекс этого массива - номер 
        // шаблона на который была заменена строка в массиве $jsCode  
        for ($i = 0; $i < count($strings[0]); $i++) { 
          $ordered[$i] = $strings[0][$i]; 
          $jsCode = str_replace($strings[0][$i], $this->pattern($i), $jsCode); 
          $i++; 
        }*/
/*        
        // Заменяем коменты на пробел     
      //  if ($this->delComments)      
          $jsCode = preg_replace("/\/\/.+\n/", "", $jsCode);
        
        // Убиваем форматирование 
//        if ($this->formatingType){
          $jsCode = preg_replace("/:\s+/", ":", $jsCode);  
          $jsCode = preg_replace("/(\s+|\n+|\t +)/", " ", $jsCode);      
  //      }
        
        // Заменяем коменты на пробел      
//        if ($this->delComments)
          $jsCode = preg_replace("/\/\*.*?\*\//", "", $jsCode);  
        
        // Убиваем форматирование 
   //     if ($this->formatingType){
          $jsCode = preg_replace("/\s?{\s?/", "{", $jsCode);    
          $jsCode = preg_replace("/\s?}\s?/", "}", $jsCode);
          $jsCode = preg_replace("/\s?\(\s?/", "(", $jsCode);    
          $jsCode = preg_replace("/\s?\)\s?/", ")", $jsCode);         
          $jsCode = preg_replace("/,\s+/", ",", $jsCode); 
  //      }
 */        
      /*  $jsCode = trim(preg_replace("/\s?;\s?/", ";", $jsCode));
        foreach ($ordered as $i => $string) { 
          // возвращаем строки на место 
       //   $jsCode = preg_replace($this->pattern($i), "$string", $jsCode); 
        }         
 
        // Экранируем спецсимволы, что бы не мешались  
        $jsCode = html_entity_decode($jsCode);
        */ 
        return $jsCode;
    }

    public function clearFormatting($htmlCode){
        $htmlCode = preg_replace("/>\s*</", "><", $htmlCode);  

        return $htmlCode;
    }

}
