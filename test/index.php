<?php
/*
    require_once('../classes/CssCode.php');

    $c = new CI_CssCode(true, 0);

    $c->addFile('test.css');

    $c->optimizeCode();

    echo $c->getPercent();

    echo $c->getOptimizeCode();
  */
    require_once('../CI.php');
    $ci = CI::getInstance();
require_once('../classes/HtmlCode.php');
$html = new CI_HtmlCode(true, true);

        $html->addCode('
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="ru" class="ie6 ielt8"> <![endif]-->
<!--[if IE 7 ]>    <html lang="ru" class="ie7 ielt8"> <![endif]-->
<!--[if IE 8 ]>    <html lang="ru" class="ie8"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html lang="ru"> <!--<![endif]-->
<head>

  <title>8xx8.ru - 8xx8 - Творческая мастерская</title>
  <base href="http://8xx8.ru/" />
  <meta name="title" content="8xx8.ru - 8xx8 - Творческая мастерская" />
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="language" content="ru" />
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
  <link rel="stylesheet" href="http://8xx8.ru/css/main-style.css" type="text/css" media="screen" />
  <link rel="stylesheet" href="http://8xx8.ru/css/fullsize.css" type="text/css" media="screen" />

  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
  <link rel="icon" href="favicon.ico" type="image/ico">

  <script language="javascript" type="text/javascript" src="http://8xx8.ru/js/jquery.js"></script>
  <script type="text/javascript" src="http://8xx8.ru/js/jquery.fullsize.minified.js"></script>
</head>
<body>
  <div id="wrapper">
    <header><a href="http://8xx8.ru" id="logo" title="8xx8 - Творческая мастерская">8xx8</a></header>
    <section id="mainpage">
     <div id="draw"><a title="Галерея графических работ" href="рисую.html">Рисую</a></div>
<div id="write"><a title="Полезный код" href="пишу/">Пишу</a></div>
<div id="music"><a title="Музыка" href="сочиняю.html">Сочиняю</a></div>
<div id="think"><a title="Персональный блог" href="думаю/">Думаю</a></div>
    </section>
  <footer>
&copy; 8xx8.ru 2010-2011 &nbsp;-&nbsp; Андрей Кулаков
</footer>


  </div>
</body>
</html>
');
        CI_Log::write("add Html Code", "CI");

        $html->optimizeCode();

echo    $html->getOptimizedCode();
   // $ci->start();
?>
<html>
    <body>
        Fucking test;
    </body>
</html>
<?php
   // $ci->end();
?>
    