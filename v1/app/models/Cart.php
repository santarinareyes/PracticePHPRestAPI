<?php 
    /*
     * Use $this->db::connectMasterDB() to connect to the master database
     * Use $this->db::connectReadDB() to connect to the read database
     */
    class Cart {
        private $db;

        public function __construct()
        {
            $this->db = new Database;
            $this->db->connectReadDB();
        }

        public function checkUsername($username){
            $this->db->query("SELECT username, user_id FROM users 
                              WHERE username = :username");
            $this->db->bind(":username", $username);
            return $this->db->single();
        }

        public function checkUserId($id){
            $this->db->query("SELECT username, user_id FROM users 
                              WHERE user_id = :id");
            $this->db->bind(":id", $id);
            return $this->db->single();
        }

        public function checkProduct($title){
            $this->db->query("SELECT product_id, product_title, product_price 
                              FROM products WHERE product_title = :title");
            $this->db->bind(":title", $title);
            return $this->db->single();
        }

        public function addCartItem($data){
            $this->db->connectMasterDB();
            $this->db->query("INSERT INTO carts (cart_user_id, cart_product_id) 
                              VALUES (:cart_user_id, :cart_product_id)");
            $this->db->bind(":cart_user_id", $data["user"]);
            $this->db->bind(":cart_product_id", $data["product"]);

            return trueOrFalse($this->db->execute());
        }

        public function getLastAddedCartItem(){
            $this->db::connectMasterDB();
            $lastInsertId = $this->db->lastInsertId();
            $this->db->query("SELECT c.cart_id, u.username, p.product_title FROM carts c 
                              INNER JOIN users u ON c.cart_user_id = u.user_id 
                              INNER JOIN products p ON c.cart_product_id = p.product_id 
                              WHERE cart_id = :id");
            $this->db->bind(":id", $lastInsertId);
            $this->db->execute();

            $rowCount = $this->db->rowCount();
            if($rowCount === 0){
                status500("Failed to retreive user after creation");
            }
            return $this->db->single();
        }

        public function getAllCartItems(){
            $this->db->query("SELECT c.cart_id, u.username, p.product_title FROM carts c 
                              INNER JOIN users u ON c.cart_user_id = u.user_id 
                              INNER JOIN products p ON c.cart_product_id = p.product_id 
                              GROUP BY cart_id ASC");
            return $this->db->resultSet();
        }

        public function getAllCartTotals(){
            $this->db->query("SELECT u.user_id, u.username, SUM(p.product_price) AS total FROM carts c 
                              INNER JOIN users u ON c.cart_user_id = u.user_id 
                              INNER JOIN products p ON c.cart_product_id = p.product_id 
                              GROUP BY u.username ASC");
            return $this->db->resultSet();
        }

        public function getUserCartTotal($username){
            $this->db->query("SELECT u.user_id, u.username, SUM(p.product_price) AS total FROM carts c 
                              INNER JOIN users u ON c.cart_user_id = u.user_id 
                              INNER JOIN products p ON c.cart_product_id = p.product_id 
                              WHERE username = :username 
                              GROUP BY u.username ASC");
            $this->db->bind(":username", $username);
            return $this->db->single();
        }

        public function getIdCartTotal($id){
            $this->db->query("SELECT u.user_id, u.username, SUM(p.product_price) AS total FROM carts c 
                              INNER JOIN users u ON c.cart_user_id = u.user_id 
                              INNER JOIN products p ON c.cart_product_id = p.product_id 
                              WHERE user_id = :id 
                              GROUP BY u.username ASC");
            $this->db->bind(":id", $id);
            return $this->db->single();
        }

        public function deleteUserCart($id){
            $this->db->connectMasterDB();
            $this->db->query("DELETE FROM carts WHERE cart_user_id = :id");
            $this->db->bind(":id", $id);
            return trueOrFalse($this->db->execute());
        }

        public function checkCartItem($id){
            $this->db->query("SELECT u.username, c.cart_id, p.product_title, p.product_price FROM carts c 
                              INNER JOIN products p ON c.cart_product_id = p.product_id 
                              INNER JOIN users u ON c.cart_user_id = u.user_id 
                              WHERE c.cart_id = :id");
            $this->db->bind(":id", $id);
            return $this->db->single();
        }

        public function deleteCartItem($id){
            $this->db->connectMasterDB();
            $this->db->query("DELETE FROM carts WHERE cart_id = :id");
            $this->db->bind(":id", $id);
            return trueOrFalse($this->db->execute());
        }

        public function countAllCarts(){
            $this->db->query("SELECT count(*) FROM carts 
                              GROUP BY cart_user_id");
            return $this->db->fetchColumn();
        }

        public function getCartsPagination($limitPerPage, $offset){
            $this->db->query("SELECT u.user_id, u.username, SUM(p.product_price) AS total FROM carts c 
                              INNER JOIN users u ON c.cart_user_id = u.user_id 
                              INNER JOIN products p ON c.cart_product_id = p.product_id 
                              GROUP BY u.username 
                              ORDER BY cart_id ASC
                              LIMIT :limit OFFSET :offset");
            $this->db->bind(":limit", $limitPerPage);
            $this->db->bind(":offset", $offset);
            return $this->db->resultSet();
        }
    }