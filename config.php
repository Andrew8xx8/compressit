<?php
return array (
    'web_path' => "http://".$_SERVER['SERVER_NAME']."/compressit/test",
    'site_path' => $_SERVER['DOCUMENT_ROOT'],
    'cache_dir' => 'c:\\xampp\\htdocs\\compressit\\test\\cache',
    'web_cache_dir' => "http://".$_SERVER['SERVER_NAME']."/compressit/test/cache",
    'logFile' => '../logs/main_log.txt',
	'folder_delimiter' => '\\',
	'logLevel' => 10,
    'gzip' => false
);
 
