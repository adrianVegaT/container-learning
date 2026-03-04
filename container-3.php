<?php

/**
 * Este container tendra la funcionalidad de Reflector 
 * para resolver las dependencias de las clases automaticamente (Auto-resolución)
 */

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
        $reflector = new ReflectionClass($class);

        $constructor = $reflector->getConstructor();

        if (!$constructor) {
            return new $class;
        }

        $parameters = $constructor->getParameters();

        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if (!$type || $type->isBuiltin()) {
                throw new Exception("Cannot auto-resolve {$type->getName()}");
            }

            $dependencies[] = $this->resolve($type->getName());
        }

        return $reflector->newInstanceArgs($dependencies);

    }

    public function make($key)
    {
        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        }

        if (!isset($this->bindings[$key])) {
            return $this->resolve($key);
        }

        // if (isset($this->bindings[$key])) {
            $resolver = $this->bindings[$key];
            $instance = $resolver($this);

            if (isset($this->bindings[$key . 'singleton'])) {
                $this->instances[$key] = $instance;
            }

            return $instance;
        // }


    }
}

class Database
{
    public function __construct()
    {
        echo "Database creado\n";
    }
}

class Logger
{
    public function __construct()
    {
        echo "Logger creado\n";
    }
}

class UserRepository
{
    private $db;
    private $logger;

    public function __construct(Database $db, Logger $logger)
    {
        $this->db = $db;
        $this->logger = $logger;
        echo "UserRepository creado\n";
    }
}

// Magia: Container resuelve AUTOMÁTICAMENTE
$container = new Container();

// $userRepo = $container->make(UserRepository::class);
$container->make(UserRepository::class);
