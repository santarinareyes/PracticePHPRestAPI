<?php 
    class Session {
        private $db;

        public function __construct()
        {
            $this->db = new Database;
            $this->db::connectMasterDB();
        }

        public function updateLoginAttempts($id){
            $this->db->query("UPDATE users SET loginattempts = loginattempts+1 
                              WHERE user_id = :id");
            $this->db->bind(":id", $id);
            $this->db->execute();
        }
    }