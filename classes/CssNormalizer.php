<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Andrew8xx8
 * Date: 08.04.11
 * Time: 23:47
 * To change this template use File | Settings | File Templates.
 */
require_once  ('Normalizer.php');

class CI_CssNormalizer extends CI_Normalizer{
    public function normalize($cssCode){
        $cssCode = $this->removeCrlf($cssCode);

        $cssCode = $this->normalizeMetrics($cssCode);
        $cssCode = $this->normalizeColors($cssCode);
        $cssCode = $this->clearFormatting($cssCode);
         //$cssCode = preg_replace("/:\s+/", ":", $cssCode);
      /*  $string = 'April 15, 2003';
$pattern = '/(\w+) (\d+), (\d+)/i';
$replacement = '${1} $2, $3';
echo preg_replace($pattern, $replacement, $string);
        echo preg_replace("%%{}{4}", ":", $cssCode);
        die;*/
        return $cssCode;
    }

    /**
     * Убирает в  переданном Css коде все определения типа для ноля,
     * например из 0px => 0
     * @param  string $cssCode
     * @return string
     */
    public function normalizeMetrics($cssCode){
        return preg_replace("/(\s|:)(0em|0ex|0%|0px|0cm|0mm|0in|0pt|0pc)/i", " 0", $cssCode);
    }

    public function normalizeColors($cssCode){
        preg_match_all("/#(aa|bb|cc|dd|ee|ff|11|22|33|44|55|66|77|88|99|00){3}/i", $cssCode, $colors);

        foreach($colors[0] as $color) {
            if ((substr($color, 1, 1) == substr($color, 2, 1)) ||
                (substr($color, 3, 1) == substr($color, 4, 1)) ||
                    (substr($color, 5, 1) == substr($color, 6, 1))
            )
            $cssCode = preg_replace("/$color/i", "#".substr($color, 1, 1).substr($color, 4, 1).substr($color, 6, 1), $cssCode);
        }
        return $cssCode;
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

    private function addSpaceAfterPseudoElements($cssCode){
        // Фиксим багу ие шестого c псевдо элементами
        $cssCode = preg_replace('/:first-l(etter|ine)\\{/', ':first-l$1 {', $cssCode);
        $cssCode = preg_replace('/:(active|after|before|first-child|first-letter|first-line|focus|hover|lang|link|visited|)\\{/', ':$1 {', $cssCode);
        return $cssCode;
    }
}
