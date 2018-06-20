<?php

namespace Router\Storage;

interface StorageInterface {

    /**
     * read data from storage
     * @return string
     */
    public function read() : ?string;

    /**
     * write data to storage
     * @param string $data 
     * @return void
     */
    public function write(string $data) : void;

    /**
     * remove data from storage
     * @return void
     */
    public function remove() : void;

    /**
     * check if data exists
     * @return bool
     */
    public function has() : bool;

    /**
     * change storage key/property
     * @param string $key 
     * @return void
     */
    public function useKey( string $key ) : void;

    /**
     * return in use key
     * @return string
     */
    public function key() : string;

}