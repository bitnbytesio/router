<?php

use PHPUnit\Framework\TestCase;

use Router\{Router,Manager};
use Router\Commands\HttpCommand;

class ManagerTest extends TestCase {


    public function setUp() {
        $_SERVER['SCRIPT_NAME'] = 'index.php';
        $_SERVER['REQUEST_URI'] = '/';
    }

    /**
     * @dataProvider routes
     */
    public function testMatch(Router $r) {

        $manager = new Manager;
   

        $this->assertInstanceOf(Router::class, $r);

        $stack = [
            ['GET', '/product'],
            ['GET', '/product/1'],
            ['POST', '/product'],
            ['PUT', '/product/1'],
            ['DELETE', '/product/1'],
        ];
            
        foreach ($stack as $item) {
            $_SERVER['REQUEST_METHOD'] = $item[0];
            
            $this->assertInstanceOf(HttpCommand::class, $manager->match($r, $item[1], $item[0]), $item[1] );
        }


         $stack = [
            ['GET', '/fake'],
            ['GET', '/fake/1'],
            ['POST', '/fake'],
            ['PUT', '/fake/1'],
            ['DELETE', '/fake/1'],
        ];
            
        foreach ($stack as $item) {
            $_SERVER['REQUEST_METHOD'] = $item[0];
            $this->assertNull( $manager->match($r, $item[1], $item[0]) , $item[1] );
        }


        


    }

 
    public function routes() {

        $r = new Router();

        $r = $r->add('GET', '/product', 'before', 'all', 'after')
        ->add('GET', '/product/:id', 'before', 'single', 'after')
        ->add('POST', '/product', 'before', 'create', 'after')
        ->add('PUT', '/product/:id', 'before', 'update', 'after')
        ->add('DELETE', '/product/:id', 'before', 'delete', 'after');

        
        return [ 
            [$r],
        ];
    }

}