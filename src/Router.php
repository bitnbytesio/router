<?php

namespace Router;

use Router\Commands\HttpCommand;
use Router\Exceptions\RouterException;

class Router implements RouterInterface {

    // store route prefix  
    public $prefix = '';

    // routes collection
    public $stack = [];

    // supported http methods
    const HTTP_METHODS = [
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'OPTIONS',
        'HEAD',

    ];

    /**
     * setup router
     * @param string $prefix 
     * @param string ...$middlewares 
     * @return self
     */
    public function __construct(string $prefix = '', ...$middlewares) {

        if ($prefix != '') {
            // store prefix
            $this->prefix = '/' . trim($prefix, '/');
        }

        // build stack for supported http methods
        foreach (self::HTTP_METHODS as $method) {
            $this->stack[$method] = [];
        }

    }
     
    /**
     * set routing concept
     * @param string $command 
     * @param string ...$action 
     * @return self
     */
    public function use(string $command, string ...$action) : self {

        $this->parseCommand($command, ...$action);       

        return $this;
    }

    /**
     * add http route
     * @param string $method 
     * @param string $uri 
     * @param string ...$action 
     * @return self
     */
    public function add(string $method, string $uri, string ...$action) : self {

        $uri = ($this->prefix . '/' . trim($uri, '/'));

        $this->stack[$method][] = new HttpCommand($method, $uri, ...$action);

        return $this;

    }

    /**
     * add child router
     * @param self ...$routers 
     * @return self
     */
    public function child(self ...$routers) : self {

        foreach ($routers as $router) {

            foreach ($router->collection() as $stackItem) {
                foreach ($stackItem as $item) {
                    $this->add($item->method, $item->uri, ...$item->action);
                }
            }            

        }

        return $this;

    }

    /**
     * parse command 
     * @param string $command 
     * @param string ...$action 
     * @return void
     */
    public function parseCommand(string $command, string ...$action) : void {


        $array = explode(' ', $command);
          
        $length = count($array);
      
        if (in_array(strtoupper($array[0]), self::HTTP_METHODS) && $length == 2) {
            $this->add($array[0], $array[1], ...$action);
            return;            
        }

        throw new RouterException('Invalid command ' . $command);

    }

    /**
     * get stack/collection of routes
     * @return array
     */
    public function collection() : array {
        return $this->stack;
    }

    /**
     * fake methods listener, will simulate only supported http methods
     * @param string $method 
     * @param array $params 
     * @return self
     */
    public function __call(string $method, array $params) : self {


        $method = strtoupper($method);

        if (in_array( $method, self::HTTP_METHODS)) {
            $uri = $params[0];
            unset($params[0]);
            $this->use($method . ' ' . $uri, ...$params);
        }

        return $this;

    }   

    /**
     * find route by matching uri with stack
     * @param string $method 
     * @param string $uri 
     * @return HttpCommand
     */
    public function find(string $method, string $uri) : ?HttpCommand {


        foreach ($this->stack[$method] as $item) {

            if ($item->method == $method && $item->match($uri)) {
                return $item;
            }

        }

        return null;
    }

    

}