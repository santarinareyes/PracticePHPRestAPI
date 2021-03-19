<?php 
    /*
     * Use $this->db::connectMasterDB() to connect to the master database
     * Use $this->db::connectReadDB() to connect to the read database
     */
    class User {
        private $db;

        public function __construct()
        {
            $this->db = new Database;
            $this->db->connectReadDB();
        }

        public function getSingleUser($username, $email){
            $this->db->connectMasterDB();
            $this->db->query("SELECT username, email FROM users WHERE username = :username OR email = :email");
            $this->db->bind(":username", $username);
            $this->db->bind(":email", $email);
            return $this->db->single();
        }

        public function createUser($data){
            $this->db->connectMasterDB();
            $this->db->query("INSERT INTO users (firstname, lastname, username, email, password) 
                              VALUES (:firstname, :lastname, :username, :email, :password)");
            $this->db->bind(":firstname", $data["firstname"]);
            $this->db->bind(":lastname", $data["lastname"]);
            $this->db->bind(":username", $data["username"]);
            $this->db->bind(":email", $data["email"]);
            $this->db->bind(":password", $data["password"]);

            return trueOrFalse($this->db->execute());
        }
    }