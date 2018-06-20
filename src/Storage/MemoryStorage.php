<?php

/**
 * extension: http://php.net/manual/en/book.memcache.php
 */ 

namespace Router\Storage;

/**
 * @implements StorageInterface
 */
class MemoryStorage implements StorageInterface {

    // connection instance
    protected $server;

    // storage key/property
    protected $key;

    /**
     * setup storage
     * @param string|string $host 
     * @param string|string $port 
     * @param type|string $key 
     * @return self
     */
    public function __construct(string $host = '127.0.0.1', string $port = '11211', $key = 'routes.lock') {
        
        $this->server  = new \Memcache;      
        if (! $this->server->connect($host, $port)) {
            throw new \Exception('Unable to connect memory storage. Please install/start memcached service.');
        }

        $this->key = $key;

    }

    /**
     * @inherit
     * {@inherit}
     * {@inheritdoc}
     */
    public function has() : bool {
        return !empty($this->read());
    }

    /**
     * @inherit
     * {@inherit}
     * {@inheritdoc}
     */
    public function write(string $data) : void {
        $this->server->set( $this->key, $data );
    }

    /**
     * @inherit
     * {@inherit}
     * {@inheritdoc}
     */
    public function read() : ?string {
        return  $this->server->get( $this->key );
    }

    /**
     * @inherit
     * {@inherit}
     * {@inheritdoc}
     */
    public function remove() : void {
        $this->server->delete($this->key);
    }

    /**
     * @inherit
     * {@inherit}
     * {@inheritdoc}
     */
    public function useKey(string $key) : void {
        $this->key = $key;
    }

    /**
     * @inherit
     * {@inherit}
     * {@inheritdoc}
     */
    public function key() : string {
        return $this->key;
    }
  

}