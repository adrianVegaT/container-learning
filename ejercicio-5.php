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

    public function resolve($class){
        $reflector = new ReflectionClass($class);

        $constructor = $reflector->getConstructor();

        if(!$constructor){
            return new $class;
        }

        $parameters = $constructor->getParameters();

        $dependencies = [];

        foreach($parameters as $parameter){
            $type = $parameter->getType();

            if(!$type || $type->isBuiltin()){
                throw new Exception("This type cannot be found {$parameter->getName()}");
            }

            $dependencies[] = $this->make($type->getName());
        }

        return $reflector->newInstanceArgs($dependencies);
    }

    public function make($key){
        if(isset($this->instances[$key])){
            return $this->instances[$key];
        }

        if(isset($this->bindings[$key])){
            $instance = $this->bindings[$key]($this);

            if(isset($this->bindings[$key.'singleton'])){
                $this->instances[$key] = $instance;
            }

            return $instance;
        }

        return $this->resolve($key);
        
    }
}

interface CacheInterface{
    public function get($key);
    public function set($key, $value);
}

class FileCache implements CacheInterface{
    public function get($key)
    {
        echo "Leyendo de archivo: $key\n";
        return "valor_$key";    }

    public function set($key, $value)
    {
        echo "Guardando en archivo: $key = $value\n";
    }
}

class RedisCache implements CacheInterface{
    public function get($key)
    {
        echo "Leyendo de Redis: $key\n";
        return "valor_$key";    }

    public function set($key, $value)
    {
        echo "Guardando en Redis: $key = $value\n";
    }
}

class ProductRepository {
    private $cache;
    
    public function __construct(CacheInterface $cache) {
        $this->cache = $cache;
    }
    
    public function find($id) {
        $key = "product_$id";
        
        $cached = $this->cache->get($key);
        
        if ($cached) {
            return $cached;
        }
        
        // Simular búsqueda en BD
        $product = "Producto $id de BD";
        $this->cache->set($key, $product);
        
        return $product;
    }
}

$container = new Container();
$container->bind(CacheInterface::class, fn() => new RedisCache());
$productRepo = $container->make(ProductRepository::class);
$productRepo->find(100);