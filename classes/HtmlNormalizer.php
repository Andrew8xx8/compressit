<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Andrew8xx8
 * Date: 10.04.11
 * Time: 23:19
 * To change this template use File | Settings | File Templates.
 */
require ('Normalizer.php');

class CI_HtmlNormalizer extends CI_Normalizer{
    public function normalize($htmlCode){
        $htmlCode = $this->removeCrlf($htmlCode);
        //$cssCode = $this->normalizeMetrics($cssCode);
        //$cssCode = $this->normalizeColors($cssCode);
        $htmlCode = $this->clearFormatting($htmlCode);

        return $htmlCode;
    }

    public function clearFormatting($htmlCode){
        $htmlCode = preg_replace("/>\s*</", "><", $htmlCode);  

        return $htmlCode;
    }

}
