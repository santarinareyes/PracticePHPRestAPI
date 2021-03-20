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

        public function countAllUsers(){
            $this->db->query("SELECT count(*) FROM users");
            return $this->db->fetchColumn();
        }

        public function getUsersPagination($limitPerPage, $offset){
            $this->db->query("SELECT * FROM users 
                              ORDER BY user_id ASC
                              LIMIT :limit OFFSET :offset");
            $this->db->bind(":limit", $limitPerPage);
            $this->db->bind(":offset", $offset);
            return $this->db->resultSet();
        }

        public function updateUser($query, $id, $newFirstname, $newLastname, $newUsername, $newEmail, $newPassword, $newRole){
            $this->db->connectMasterDB();
            $this->db->query("UPDATE users SET ".$query." WHERE user_id = :id");
            $this->db->bind(":id", $id);

            if($newFirstname != ""){
                $this->db->bind(":firstname", $newFirstname);
            }

            if($newLastname != ""){
                $this->db->bind(":lastname", $newLastname);
            }

            if($newUsername != ""){
                $this->db->bind(":username", $newUsername);
            }

            if($newEmail != ""){
                $this->db->bind(":email", $newEmail);
            }

            if($newPassword != ""){
                $this->db->bind(":password", $newPassword);
            }

            if($newRole != ""){
                $this->db->bind(":role", $newRole);
            }

            $this->db->execute();

            $rowCount = $this->db->rowCount();
            if($rowCount === 0){
                status500("Failed to update task");
            }
        }

        public function getUpdatedUser($id){
            $this->db->connectMasterDB();
            $this->db->query("SELECT * FROM users WHERE user_id = :id");
            $this->db->bind(":id", $id);
            return $this->db->single();
        }

        public function deleteUser($id){
            $this->db->query("DELETE FROM users WHERE user_id = :id");
            $this->db->bind(":id", $id);
            return trueOrFalse($this->db->execute());
        }
    }