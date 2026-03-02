<?php

class Container
{
    private $bindings;

    public function bind($key, $resolver)
    {
        return $this->bindings[$key] = $resolver;
    }

    public function make($key)
    {
        if (!isset($this->bindings[$key])) {
            return new Exception("This bind cannot resolve");
        }
        
        $resolver = $this->bindings[$key];

        return $resolver($this);
    }
}