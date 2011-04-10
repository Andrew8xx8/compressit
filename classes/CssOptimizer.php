<?php 
/**
 * CssCode.php Класс для сжатия CSS кода по средствам удаления лишних пробелов символов табуляции и переводов строк.
 *
 * @author    AndrewKvik
 * @version   0.1a
 * @copyright ChaosLab
 */


require_once("Optimizer.php");

  function opMargin(&$itm){        
    //echo "<pre>";print_r($itm);		

// Свёртка margin и padding
    $boxes = Array('margin', 'padding');
    foreach($boxes as $box){
      if (isset($itm['descr'][$box.'-top']) && isset($itm['descr'][$box.'-bottom']) 
      && isset($itm['descr'][$box.'-left']) && isset($itm['descr'][$box.'-right'])) {                         
        if (($itm['descr'][$box.'-top'] == $itm['descr'][$box.'-bottom']) &&
        ($itm['descr'][$box.'-right'] == $itm['descr'][$box.'-left'])){
          if ($itm['descr'][$box.'-right'] == $itm['descr'][$box.'-bottom']) {
            // Свёртка в rule: ALL
            $itm['descr'][$box] = $itm['descr'][$box.'-top'];        
          } else 
          // Свёртка в rule: TOP-BOTTOM LEFT-RIGHT
          $itm['descr'][$box] = $itm['descr'][$box.'-top'].' '.$itm['descr'][$box.'-left'];        
        } elseif ($itm['descr'][$box.'-left'] == $itm['descr'][$box.'-right'])
          // Свёртка в rule: TOP LEFT-RIGHT BOTTOM
          $itm['descr'][$box] = $itm['descr'][$box.'-top'].' '.$itm['descr'][$box.'-right'].' '.$itm['descr'][$box.'-bottom'];        
        else
          // Свёртка в rule: 0 0 0 0
          $itm['descr'][$box] = $itm['descr'][$box.'-top'].' '.$itm['descr'][$box.'-right'].' '.$itm['descr'][$box.'-bottom'].' '.$itm['descr'][$box.'-left'];        
        unset($itm['descr'][$box.'-top']);
        unset($itm['descr'][$box.'-bottom']);
        unset($itm['descr'][$box.'-left']);
        unset($itm['descr'][$box.'-right']);
      }
    }  

// Свёртка по background
    foreach (Array('image', 'color', 'position', 'repeat', 'attachment') as $bgparam)    
      if (isset($itm['descr']['background-'.$bgparam])) {
        if ($bgparam == 'attachment')         
          $itm['descr']['background'][$bgparam] = $itm['descr']['background-'.$bgparam][0].' '.$itm['descr']['background-'.$bgparam][1];
        else
          $itm['descr']['background'][$bgparam] = $itm['descr']['background-'.$bgparam];
        unset($itm['descr']['background-'.$bgparam]);
      }    
   
  }
  
  function calcHash($value){        
    ksort($value['descr']);
    $hash = '';
    foreach($value['descr'] as $k=>$v){      
			if (gettype($v) == 'array') {
          $hash .= $k.':';
          foreach($v as $v1)
            $hash .= ' '.$v1;
        } else 
          $hash .= $k.':'.$v;
    }  
    return md5($hash);
  }
  
  function hashSort($a, $b){   
    return strcmp($a['hash'], $b['hash']);
  }
  
  function rateSort($a, $b){   
    if ($a['rate'] == $b['rate']) {
        return 0;
    }
    return ($a['rate'] < $b['rate']) ? -1 : 1;
  }
  
class CI_CssOptimizer extends CI_Optimizer{

    public function __construct(){
        
    }
    /**
     * @param  $cssCode
     * @return string
     */
    public function optimize($cssCode){
        return $cssCode;
        /*
         *

        // Заменяем коменты на пробел
        if ($this->delComments)
            $result = preg_replace("/\/\*.*?\*\//", "", $result);

        $result = trim(preg_replace("/\s?;\s?/", ";", $result));

        $css = $this->splitCSS($result);
      //  $cssOp = new CSSOptimize();
//        $result = $cssOp->optimize($css);



        return $css;*/
    }




  // Разбивает селектор на составляющие
  // body,div,dl
  // ||
  // \/
  // Array ( [0] => body [1] => div [2] => dl )
  protected function splitSelector($selector) {
    $result = preg_split("/,/", $selector);
    return $result;
  }

  // Определяет тип параметра
  // [background] => Array
  //              (
  //                  [0] => url(../i/bg-info-search.gif)
  //                  [1] => no-repeat
  //                  [2] => 100%
  //                  [3] => 100%
  //              )
  // ||
  // \/
  // [background] => Array
  //              (
  //                  [image] => url(../i/bg-info-search.gif)
  //                  [repeat] => no-repeat
  //                  [top] => 100%
  //                  [left] => 100%
  //              )
      protected function detectParamsType($rule, $params){
        if (gettype($params) == "array"){
          $result = Array();
          if ($rule == 'background') {
            for ($i = 0; $i < count($params) - 1; $i++)
              if ((preg_match("/^(1|2|3|4|5|6|7|8|9|0)+/", $params[$i]))
              && (preg_match("/^(1|2|3|4|5|6|7|8|9|0)+/", $params[$i+1]))){
                $result['position'] = $params[$i].' '.$params[$i + 1];
                unset( $params[$i]);
                unset( $params[$i + 1]);
              }
          }
          foreach($params as $name=>$param){
            if (preg_match("/^(fixed|normal)/", $param))
              $result['attachment'] = $param;
            elseif (preg_match("/^(no-)?repeat(-x|-y)?/", $param))
              $result['repeat'] = $param;
            elseif (preg_match("/^url\(/", $param))
              $result['image'] = $param;
            elseif (preg_match("/^#/", $param))
              $result['color'] = $param;
            else $result[$name] = $param;
          }
          return $result;
        } else return $params;
      }

      // Разбивает правило на составляющие
      // display: block;
      // border: 1px solid #aaa;
      // ||
      // \/
      // Array ( [display] => block [border] => Array ( [0] => 1px [1] => solid [2] => #aaa )
      protected function splitDescriptor($descriptor) {
        $descriptor = $descriptor.';;';
        // font:12px/18px Verdana,sans-serif;height:100%;background:#f2f5e3;
        // ||
        // \/
        // Array ( [0] => Array ( [0] => font:12px/18px Verdana,sans-serif; [1] => height:100%; [2] => background:#f2f5e3; ) [1] => Array ( [0] => font [1] => height [2] => background ) [2] => Array ( [0] => 12px/18px Verdana,sans-serif [1] => 100% [2] => #f2f5e3 ) )
        preg_match_all("/(.*?\s*?):\s*?(.*?\s*?)[;]/", $descriptor, $pairs);
        $result = array();
        for($i=0; $i < count($pairs[1]); $i++ ){
          // 12px/18px Verdana,sans-serif
          // ||
          // \/
          // Array ([0] => 12px/18px [1] => Verdana,sans-serif )
          $rule = preg_split('/ /', $pairs[2][$i], -1, PREG_SPLIT_NO_EMPTY);
          if (count($rule) == 1)
            $result[$pairs[1][$i]] = $rule[0];
          else
            $result[$pairs[1][$i]] = $rule;
        }
        return $result;
      }

      // Разбирает css на составляющие
      protected function splitCSS($css) {
        preg_match_all("/(.*?){(.*?)}/", $css, $pairs);
        $result = Array();
        $count = count($pairs[1]);
        for($i = 0; $i < $count; $i++){
          $descr = $this->splitDescriptor($pairs[2][$i]);
          foreach($this->splitSelector($pairs[1][$i]) as $sel){
            $result[] = Array ('selector' => $sel, 'descr' => $descr, 'rate' => $i);
          }
        }
        return $result;
      }
  
  public function optimize1($css){
    $result = '';  
    array_walk(&$css, "opMargin");
    // Считаем хеш для набора свойств каждого селектора
    for($i = 0; $i < count($css); $i++)
      $css[$i]['hash'] = calcHash($css[$i]);
      
    uasort($css, 'hashSort');  		
    
		$css1 = Array();
		
		foreach($css as $selector) {
			if (isset($css1[$selector['hash']])) 
			  $css1[$selector['hash']]['selector'] .= ','.$selector['selector'];
			else  
			  $css1[$selector['hash']] = $selector;
		}
		
		$css = $css1;

    uasort($css, 'rateSort');  
    $result = '';  
    foreach($css as $sel) {
      if (empty($sel['selector'])) continue;
      $result .= $sel['selector'].'{';      
      foreach($sel['descr'] as $k => $v) {
        if (gettype($v) == 'array') {
          $result .= $k.':';
          foreach($v as $v1)
            $result .= ' '.$v1;
        } else 
          $result .= $k.':'.$v;
        $result .= ';';
      }
			$result .= '}';
    }    
    return $result;
  }
  
  
  protected function opPadding(){
  }
}
?>