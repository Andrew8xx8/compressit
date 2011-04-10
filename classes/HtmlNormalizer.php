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
        //$cssCode = $this->clearFormatting($cssCode);

        return $htmlCode;
    }

    public function clearFormatting($cssCode){
        $cssCode = preg_replace("/:\s+/", ":", $cssCode);
        $cssCode = preg_replace("/(\s+|\n+|\t+)/", " ", $cssCode);
        $cssCode = preg_replace("/\s?{\s?/", "{", $cssCode);
        $cssCode = preg_replace("/\s?;?\s*}\s?/", "}", $cssCode);
        $cssCode = preg_replace("/,\s+/", ",", $cssCode);
        $cssCode = preg_replace("/(\s+|\n+|\t+)/", " ", $cssCode);
        $cssCode = preg_replace("%/(\s+)/%", " ", $cssCode);

        $cssCode = preg_replace('%@import\\s+url%', '@import url', $cssCode);
        $cssCode = preg_replace("/@(.*?);\s/", "@$1;", $cssCode);

        $cssCode = $this->addSpaceAfterPseudoElements($cssCode);

        return $cssCode;
    }

}
