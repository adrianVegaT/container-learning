<?php

include 'container.php';

$container = new Container();

$container->bind('saludo', function(){
    return 'Hola mundo';
});

echo $container->make('saludo');