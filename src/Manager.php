<?php

namespace Router;

use Router\Storage\{FileStorage,StorageInterface};
use Router\Commands\HttpCommand;

class Manager {

    // redirect base
    public $redirectBase = '';
    
    // request uri
    public $requestUri = null;

    // do we need to lock router
    protected $lock = false;

    // is router locked
    protected $locked = false;
    
    // storage
    protected $storage;

    public function __construct() {

        // setup redirect base
        $this->redirectBase = str_ireplace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

        // guess request uri
        $this->requestUri = strtok($_SERVER['REQUEST_URI'], '?');

        if ($this->redirectBase  != '/') {
            $this->requestUri =  '/' . trim( str_ireplace($this->redirectBase, '', $this->requestUri) , '/');
        }
     
    }

    /**
     * match by method and url in provided stack
     * @param RouterInterface $router 
     * @param string|null $uri 
     * @param string|null $method 
     * @return HttpCommand
     */
    public function match(RouterInterface $router, string $uri = null, string $method = null) : ?HttpCommand {

        if (is_null($uri)) {
            $uri = $this->requestUri;
        }

        if (is_null($method)) {
            $method = $this->guessRequestMethod();
        }        

        if ($this->lock == true && !$this->locked) {
            $this->lockRoutes($router);
        }

        return $router->find($method, $uri);
        
    }    

    /**
     * guess request method
     * @return string
     */
    public function guessRequestMethod() :string {

        if(isset($_REQUEST['_method'])) {
            return strtoupper($_REQUEST['_method']);
        }       

        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * check if routing is locked
     * @return bool
     */
    public function isLocked() : bool {
        return $this->lock && $this->locked;
    }

    /**
     * needs to lock router
     * @return void
     */
    public function lock() : void {
        $this->lock = true;
    }

    /**
     * freeze routing/change status to locked
     * @return void
     */
    public function freeze() : void {
        $this->lock = true;
        $this->locked = true;
    }

    /**
     * unlock routing
     * @return void
     */
    public function unlock() : void {
        $this->locked = false;
        $this->lock = false;

        $this->storage->remove();
    }

    /**
     * lock routing
     * @param Router $router 
     * @return void
     */
    protected function lockRoutes(Router $router) : void {

        $stack = [];

        foreach (Router::HTTP_METHODS as $method) {
            $stack[$method] = ['pattern' => '', 'length' => 0, 'actions' => []];

             foreach ($router->stack[$method] as $item) {
                
                $placeholders = '';
                $placeholders = implode('', array_fill(0, ( $stack[$item->method]['length'] - ($item->paramsLength - 1) ), '()'));                
              

                $stack[$item->method]['pattern'] .= "{$item->pattern}{$placeholders}|";

                $stack[$item->method]['actions'][] =  $item->action;

                $stack[$item->method]['uri'][] =  $item->uri;

                $stack[$item->method]['length']++;
            }

             $stack[$item->method]['pattern'] = rtrim( $stack[$item->method]['pattern'], '|');

        }  
       
        $this->storage->write(serialize($stack));

        $this->freeze();
    }

    /**
     * get router
     * @param type ...$arguments 
     * @return RouterInterface
     */
    public function router(...$arguments) : RouterInterface {

        if ($this->isLocked()) {
            return new LockedRouter( unserialize($this->storage->read()) );
        }


        return new Router(...$arguments);
    }

    /**
     * set storage
     * @param StorageInterface $storage 
     * @return void
     */
    public function storage(StorageInterface $storage) : void {
        $this->storage = $storage;

        if ($this->storage->has()) {
            $this->freeze();
        }
    }

       
    
}