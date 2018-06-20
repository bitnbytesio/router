<?php

use PHPUnit\Framework\TestCase;

use Router\Router;
use Router\Commands\HttpCommand;

class RouterTest extends TestCase {

    /**
     * @dataProvider routes
     */
    public function testUse(Router $r) {
   

        $this->assertInstanceOf(Router::class, $r);

        $this->assertInstanceOf(HttpCommand::class, $r->stack['GET'][0]);


    }

    /**
     * @expectedException Router\Exceptions\RouterException
     */
    public function testUseException() {
        $r = new Router();
       
        $r->use('', 'before', 'controller', 'after');
       
    }

    /**
     * @expectedException Router\Exceptions\RouterException
     */
    public function testParseCommandException() {
        $r = new Router();
       
        $r->parseCommand('', 'before', 'controller', 'after');
       
    }

     /**
     * @dataProvider routes
     */
     public function testAdd(Router $r) {        

        $this->assertNotEmpty($r->stack);
        $this->assertNotEmpty($r->stack['GET']);

        $this->assertInstanceOf(HttpCommand::class, $r->stack['GET'][0]);

    }

    /**
     * @dataProvider routes
     */
    public function testChildAndFind(Router $r) {
        
        $p = new Router('api');

        $p->child($r);

        $this->assertNull($p->find('GET', '/api/uber'));

        $this->assertInstanceOf(HttpCommand::class, $p->find('GET', '/api/lyft'));

    }

     /**
     * @dataProvider routes
     */
    public function testCollection(Router $r) {
        $p = new Router('api');

        $c = $p->collection();

        $this->assertNotEmpty($c);

        foreach (Router::HTTP_METHODS as $method) {
            $this->assertEmpty($c[$method]);
        }

        $c = $r->collection();

        $this->assertNotEmpty($c);
        $this->assertNotEmpty($c['GET']);
    }


    public function routes() {

        $r = new Router();

        $r = $r->add('GET', '/lyft', 'before', 'controller', 'after');

        
        return [ 
            [$r],
        ];
    }

}