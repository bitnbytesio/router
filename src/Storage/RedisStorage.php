<?php

namespace Router\Storage;

/**
 * @implements StorageInterface
 */
class RedisStorage implements StorageInterface {

    // server connection
    protected $conn;

    // storage key/property
    protected $key;

    /**
     * setup storage
     * @param string|string $host 
     * @param type|string $key 
     * @return self
     */
    public function __construct(string $host = 'tcp://127.0.0.1:6379', $key = 'routes.lock') {
        
       $this->conn = @stream_socket_client($host, $errno, $errstr);

       if (!$this->conn) {
            throw new \RuntimeException($errstr, $errno);
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
        $args = ['set',  $this->key, $data];
        $this->_write( $args );
    }

    /**
     * @inherit
     * {@inherit}
     * {@inheritdoc}
     */
    public function read() : ?string {
         $args = ['get',  $this->key];
         $this->_write( $args );
        return $this->_read();
    }

    /**
     * @inherit
     * {@inherit}
     * {@inheritdoc}
     */
    public function remove() : void {
        $args = ['del',  $this->key];
        $this->_write( $args );
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

    /**
     * read redis stream
     * @return mixed
     */
    private function _read() {

        $line = fgets($this->conn);

        list($type, $result) = array($line[0], substr($line, 1, strlen($line) - 3));

        if ($type == '-') { // error message
            throw new \Exception($result);

        } elseif ($type == '$') { // bulk reply

            if ($result == -1) {
                $result = null;
            } else {
                $line = fread($this->conn, $result + 2);
                $result = substr($line, 0, strlen($line) - 2);
            }

        } elseif ($type == '*') { // multi-bulk reply

            $count = ( int ) $result;
            for ($i = 0, $result = array(); $i < $count; $i++) {
                $result[] = $this->read();
            }

        } else {
            $result = $this->read();
        }

        if ($result == 'OK') {
            return $this->read();
        }
   
        return $result;

    }

    /**
     * write to redis stream
     * @param array $args 
     * @return void
     */
    private function _write(array $args) : void {

        $cmd = '*' . count($args) . "\r\n";

        foreach ($args as $item) {
            $cmd .= '$' . strlen($item) . "\r\n" . $item . "\r\n";
        }

       fwrite($this->conn, $cmd);
    }

    /**
     * close server connection
     * @return type
     */
    public function close() {
        fclose($this->conn);
        $this->conn = null;
    }

    public function __destruct() {
        $this->close();
    }
  

}