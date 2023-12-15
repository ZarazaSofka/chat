<?php

class Chat {
    public $id;
    public $name;
    public $public;
    public $description;

    public function __construct($id, $name, $public, $description) {
        $this->id = $id;
        $this->name = $name;
        $this->public = $public;
        $this->description = $description;
    }
}