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
        if (isset($this->bindings[$class])) {
            $resolver = $this->bindings[$class];
            return $resolver($this);
        }

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

            $dependencies[] = $this->make($type->getName());
        }

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

// Interfaces
interface NotificationInterface
{
    public function send($message);
}

// Implementaciones
class EmailNotification implements NotificationInterface
{
    public function send($message)
    {
        echo "📧 Email: $message\n";
    }
}

class SmsNotification implements NotificationInterface
{
    public function send($message)
    {
        echo "📱 SMS: $message\n";
    }
}

// Servicio que usa notificación
class UserService
{
    private $notifier;

    public function __construct(NotificationInterface $notifier)
    {
        $this->notifier = $notifier;
    }

    public function registerUser($name)
    {
        echo "Registrando usuario: $name\n";
        $this->notifier->send("Bienvenido $name!");
    }
}

$container = new Container();
$container->bind(NotificationInterface::class, fn() => new SmsNotification());
$userService = $container->make(UserService::class);
$userService->registerUser('Adrian');