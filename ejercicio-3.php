<?php

class Container
{
    private $bindings = [];
    private $instances = [];

    public function bind($key, $resolver)
    {
        $this->bindings[$key] = $resolver;
    }

    public function singleton($key, $resolver)
    {
        $this->bindings[$key] = $resolver;
        $this->bindings[$key . 'singleton'] = true;
    }

    public function resolve($class)
    {
        //instaciar la clase reflexion
        $reflector = new ReflectionClass($class);

        //verificar si la clase a resolver tiene constructor
        $constructor = $reflector->getConstructor();

        //si no tiene
        if (!$constructor) {
            return new $class;
        }

        //Si tiene verificar cuales son los parametros del contructior
        $parameters = $constructor->getParameters();

        $dependencies = [];

        foreach ($parameters as $parameter) {
            //Obtener el tipo del parametro
            $type = $parameter->getType();

            //Si no lo puede resolver
            if (!$type || $type->isBuiltin()) {
                throw new Exception("This type cannot be resolved {$type->getName()}");
            }

            //Llamar recursivamente esta funcion pasando el nombre del tipo de parametro y guardarla en array despendencies
            $dependencies[] = $this->resolve($type->getName());
        }

        //retornar resultado
        return $reflector->newInstanceArgs($dependencies);
    }

    public function make($key)
    {
        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        }

        if (isset($this->bindings[$key])) {
            $resolver = $this->bindings[$key];
            $instance = $resolver($this);

            if (isset($this->bindings[$key . 'singleton'])) {
                $this->instances[$key] = $instance;
            }

            return $instance;
        }

        return $this->resolve($key);
    }
}

class Config
{
    private $settings = ['app_name' => 'Mi app'];

    public function get($key)
    {
        return $this->settings[$key] ?? null;
    }
}

class Database
{
    public function __construct(private Config $config)
    {
        echo "Database conectado para: " . $config->get('app_name') . "\n";
    }
}

class UserService
{
    public function __construct(private Database $database){
        echo "UserService iniciado\n";
    }
}

$container = new Container();

$container->make(UserService::class);

