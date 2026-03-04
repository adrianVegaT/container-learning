<?php

class Container{
    private $bindings;
    private $instances;

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
            throw new Exception("This bind cannot be resolved");
        }

        $resolver = $this->bindings[$key];
        $instance = $resolver($this);

        if(isset($this->bindings[$key.'singleton'])){
            $this->instances[$key] = $instance;
        }

        return $instance;
    }
}

