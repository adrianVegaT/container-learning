<?php
require_once 'database.php';
class Mailer {
    private $db;
    
    public function __construct() {
        $this->db = new Database();  // ❌ Problema: Otra instancia
    }
    
    public function sendWelcomeEmail($userId) {
        // Usa database para buscar email del usuario
        $mailResult = $this->db->query("SELECT email FROM users WHERE id = $userId");
        $email = $mailResult->fetch(PDO::FETCH_ASSOC)['email'];
        return $email;
    }
}