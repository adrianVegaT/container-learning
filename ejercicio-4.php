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
                throw new Exception("This type not found {$type->getName()}");
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

interface NotificationInterface{
    public function send($message);
}

class SmsNotification implements NotificationInterface{
    public function send($message){
        return "Sms envidado: $message";
    }
}

class EmailNotification implements NotificationInterface{
    public function send($message){
        return "Email envidado: $message";
    }
}

class UserService{
    public function __construct(private NotificationInterface $notificationInterface){}

    public function notification($message){
        echo $this->notificationInterface->send($message);
    }
}

$container = new Container();
$container->bind(NotificationInterface::class, fn(): EmailNotification => new EmailNotification());
$userService = $container->make(UserService::class);
$userService->notification("Notificación para el usuario \n");

