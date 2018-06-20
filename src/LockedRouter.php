<?php

namespace Router;

use Router\Commands\HttpCommand;

class LockedRouter implements RouterInterface {

    protected $stack;

    /**
     * setup locked router
     * @param array $stack 
     * @return self
     */
    public function __construct( array $stack) {
        $this->stack = $stack;
    }

    /**
     * fake method listner
     * @param string $method 
     * @param array $params 
     * @return self
     */
    public function __call(string $method, array $params) {
        return $this;           
    }

    /**
     * find route by matching uri with stack
     * @param string $method 
     * @param string $uri 
     * @return HttpCommand
     */
    public function find(string $method, string $uri) : ?HttpCommand {



        preg_match('~^(?|' . $this->stack[$method]['pattern'] . ')$~', $uri, $matches);

        if (empty($matches)) {
            return null;
        }


        $position = count($matches) - 2;

        $action = null;

        if (!isset($this->stack[$method]['uri'][$position])) {
            return null;
        }

        if (isset($this->stack[$method]['actions'][$position])) {
            $action = $this->stack[$method]['actions'][$position];
        }

        $uri = $this->stack[$method]['uri'][$position];

        return new HttpCommand($method, $uri, ...$action);
       
    }

}