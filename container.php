<?php

class Container{
    private $bindings = [];

    public function bind($key, $resolve){
        return $this->bindings[$key] = $resolve;
    }

    public function make($key){
        if (!isset($this->bindings[$key])){
            throw new Exception("This bind cannot be resolved {$key}");
        }

        $resolver = $this->bindings[$key];

        if($resolver instanceof Closure){
            return $resolver($this);
        }

        return new $resolver();
    }
}