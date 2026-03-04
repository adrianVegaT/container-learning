<?php

class Container
{
    private $bindings = [];

    public function bind($key, $resolve)
    {
        return $this->bindings[$key] = $resolve;
    }

    public function make($key)
    {
        $resolver = $this->bindings[$key];

        if (!isset($resolver)) {
            throw new Exception("This bind cannot be resolved");
        }

        if ($resolver instanceof Closure) {
            return $resolver($this);
        }

        return new $resolver();
    }
}

class Greeter
{
    public function greet($name)
    {
        return "Hello $name!";
    }
}

class Calculator
{
    public function add($a, $b){
        return $a + $b;
    }
}

$container = new Container;

$container->bind('greeter', fn() => new Greeter);
$container->bind('calculator', fn() => new Calculator);

$greeter = $container->make('greeter');
$calculator = $container->make('calculator');

echo $greeter->greet("Adrian");
echo "\n";
echo $calculator->add(4, 10);
echo "\n";
