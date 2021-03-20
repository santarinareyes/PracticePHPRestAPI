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

        public function checkUserExist($username, $email){
            $this->db->connectMasterDB();
            $this->db->query("SELECT username, email FROM users WHERE username = :username OR email = :email");
            $this->db->bind(":username", $username);
            $this->db->bind(":email", $email);
            return $this->db->single();
        }

        public function createUser($data){
            $this->db->connectMasterDB();
            $this->db->query("INSERT INTO users (firstname, lastname, username, email, password, role) 
                              VALUES (:firstname, :lastname, :username, :email, :password, :role)");
            $this->db->bind(":firstname", $data["firstname"]);
            $this->db->bind(":lastname", $data["lastname"]);
            $this->db->bind(":username", $data["username"]);
            $this->db->bind(":email", $data["email"]);
            $this->db->bind(":password", $data["password"]);
            $this->db->bind(":role", $data["role"]);

            return trueOrFalse($this->db->execute());
        }

        public function getLastCreatedUser(){
            $this->db::connectMasterDB();
            $lastInsertId = $this->db->lastInsertId();
            $this->db->query("SELECT * FROM users WHERE user_id = :id");
            $this->db->bind(":id", $lastInsertId);
            $this->db->execute();

            $rowCount = $this->db->rowCount();
            if($rowCount === 0){
                status500("Failed to retreive user after creation");
            }
            return $this->db->single();
        }

        public function getAllUsers(){
            $this->db->query("SELECT * FROM users");
            return $this->db->resultSet();
        }

        public function getSingleUser($id){
            $this->db->query("SELECT * FROM users WHERE user_id = :id");
            $this->db->bind(":id", $id);
            return $this->db->single();
        }

        public function getRoleAdmin($admin){
            $this->db->query("SELECT * FROM users WHERE role = :this");
            $this->db->bind(":this", $admin);
            return $this->db->resultSet();
        }

        public function getRoleUser($user){
            $this->db->query("SELECT * FROM users WHERE role = :this");
            $this->db->bind(":this", $user);
            return $this->db->resultSet();
        }
    }