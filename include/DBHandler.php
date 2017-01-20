<?php

include_once __DIR__.'/taskController.php';



class DBHandler {
 
    private $conn;
    public $taskController;

    public function __construct() {
        require_once __DIR__.'/DBConnect.php';
        $db = new DbConnect();
        $this->conn = $db->connect();
        $this->taskController = new taskController($this->conn);
            }  

    public function getTaskController() { return $this->taskController; }
    
}
 
