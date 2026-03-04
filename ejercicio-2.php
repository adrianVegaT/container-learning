<?php 

class Container{
    private $bindings = [];
    private $instances = [];

    public function bind($key, $resolver){
        $this->bindings[$key] = $resolver;
    }

    public function singleton($key, $resolver){
        $this->bindings[$key] = $resolver;
        $this->bindings[$key.'singleton'] = true;
    }

    public function make($key){
        if(isset($this->instances[$key])){
            return $this->instances[$key];
        }

        if(!isset($this->bindings[$key])){
            throw new Exception("This bind cannot be resolved {$key}");
        }

        $resolver = $this->bindings[$key];
        $instance = $resolver($this);

        if(isset($this->bindings[$key.'singleton'])){
            $this->instances[$key] = $instance;
        }

        return $instance;
    }
}

class Database{
    public function __construct(){
        echo "Conectado a base de datos... \n";
    }

    public function query($sql){
        return "Resultado de: $sql";
    }
}

class Cache{
    public function __construct(){
        echo "Inicializando cache... \n";
    }

    public function get($key){
        return "Valor de $key \n";
    }
}

$container = new Container();

$container->singleton('database', fn() => new Database());
$container->singleton('cache', fn() => new Cache());

$container->make(key: 'database');
$container->make(key: 'database');
$container->make(key: 'database');

$container->make(key: 'cache');
$container->make(key: 'cache');
$container->make(key: 'cache');

