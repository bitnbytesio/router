<?php

namespace Router\Storage;

/**
 * @implements StorageInterface
 */
class FileStorage implements StorageInterface {

    // store file path
    protected $path;

    // store file name
    protected $file;

    /**
     * setup file storage
     * @param string $path 
     * @param string|string $file 
     * @return self
     */
    public function __construct(string $path, string $file = 'routes.lock') {
        $this->path = $path;
        $this->file = $file;
    }


    /**
     * @inherit
     * {@inherit}
     * {@inheritdoc}
     */
    public function has() : bool {
        return file_exists( $this->getFile() );
    }

    /**
     * @inherit
     * {@inherit}
     * {@inheritdoc}
     */
    public function write(string $data) : void {
        file_put_contents( $this->getFile(), $data );
    }

    /**
     * @inherit
     * {@inherit}
     * {@inheritdoc}
     */
    public function read() : ?string {
        return file_get_contents( $this->getFile() );
    }

    /**
     * @inherit
     * {@inherit}
     * {@inheritdoc}
     */
    public function remove() : void {
        @unlink($this->getFile());
    }

    /**
     * get file with path
     * @return string
     */
    public function getFile() : string {
        return rtrim($this->path, '/') . '/' . $this->file;
    }

    /**
     * @inherit
     * {@inherit}
     * {@inheritdoc}
     */
    public function useKey(string $key) : void {
        $this->file = $key;
    }

    /**
     * @inherit
     * {@inherit}
     * {@inheritdoc}
     */
    public function key() : string {
        return $this->file;
    }

}