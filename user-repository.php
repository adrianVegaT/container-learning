<?php
require_once 'database.php';
class UserRepository {
    private $db;
    
    public function __construct() {
        $this->db = new Database();  // ❌ Problema: Acoplamiento
    }
    
    public function find($id) {
        return $this->db->query("SELECT * FROM users WHERE id = $id");
    }
}