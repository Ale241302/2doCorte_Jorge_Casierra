<?php 
class Connection extends Mysqli {
    function __construct() {
        parent::__construct('localhost', 'root', '', 'ejercicio');
        $this->set_charset('utf8');
        if ($this->connect_error == NULL) {
        } else {
            die('Error de conexión: ' . $this->connect_error);
        }
    }
}

 ?>