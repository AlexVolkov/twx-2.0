<?php

ini_set('display_errors', 1);
ini_set('output_buffering', 0);
ini_set('zlib.output_compression', 0);
ini_set('output_handler', 0);
ini_set('zlib.output_handler', 0);
ini_set('proxy_buffering', 0);
apache_setenv('no-gzip', 1);

for($i = 0; $i < 10; $i++){
echo(str_repeat(' ',256));
    // check that buffer is actually set before flushing
    if (ob_get_length()){
        @ob_flush();
        @flush();
       @ob_end_flush();
    }
    @ob_start();
    echo $i;
    sleep(1);
}
?>