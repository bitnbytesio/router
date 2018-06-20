<?php

namespace Router\Commands;

class HttpCommand {

    // request method
    public $method;
    
    // request uri
    public $uri;

    // uri pattern
    public $pattern;

    // request params
    public $params = [];

    // perms length
    public $paramsLength = 0;

    // store command action/middlewares/etc
    public $action;

    /**
     * setup http command
     * @param string $method 
     * @param string $uri 
     * @param string ...$action 
     * @return self
     */
    public function __construct(string $method, string $uri, string ...$action) {

        $this->method = $method;
        $this->uri = $uri;        
        $this->pattern = preg_replace('~:([A-Za-z]*)~', '([^/]+)', $this->uri, -1, $this->paramsLength);


        if (!empty($action)) {
            $this->action = $action;
        }

    }

    /**
     * get request params
     * @param string $uri 
     * @return array
     */
    public function params(string $uri) {

        if (!empty($this->params) || $this->paramsLength == 0) {
            return $this->params;
        }

        preg_match_all('~:([A-Za-z]*)~', $this->uri, $keys);

      
        if (empty($keys[1])) {
            return $this->params;
        }

             
        preg_match("~{$this->pattern}~", $uri, $matches);

        return $this->params = array_combine($keys[1], array_splice($matches, 1));

    }

    /**
     * overwrite command actions
     * @param string ...$action 
     * @return void
     */
    public function action(string ...$action) : void {
        $this->action = $action;
    }

    /**
     * match http command by uri
     * @param string $uri 
     * @return bool
     */
    public function match(string $uri) : bool {

        return (bool) preg_match('~^' . $this->pattern . '$~', $uri);

        return !empty($matches);
    }
      

}