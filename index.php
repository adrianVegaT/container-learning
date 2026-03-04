<?php

include 'container.php';
include 'database.php';

// $container = new Container();

// $container->bind('saludo', function(){
//     return 'Hola mundo';
// });

// echo $container->make('saludo');

$container = new Container();

// $container->bind('database', function(){
//     return new Database();
// });

// $db1 = $container->make('database');
// $db2 = $container->make('database');

// $closure = fn() => 'Hola mundo';
// $string = 'Hola mundo';

// var_dump($closure instanceof Closure);
// var_dump($string instanceof Closure);

class Logger{
    public function log($message){
        echo "[LOG] $message \n";
    }
}

$container->bind('logger', function(){
    return new Logger;
});

$logger = $container->make('logger');

$logger->log("mensaje en el log modificado");