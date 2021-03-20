<?php 
    class Category {
        private $db;

        public function __construct()
        {
            $this->db = new Database;
            $this->db::connectReadDB();
        }

        public function checkCategoryExist($title){
            $this->db->query("SELECT * FROM categories WHERE category_title = :title");
            $this->db->bind(":title", $title);
            return $this->db->single();
        }

        public function getSingleCategory($id){
            $this->db->query("SELECT * FROM categories WHERE category_id = :id");
            $this->db->bind(":id", $id);
            return $this->db->single();
        }

        public function createCategory($title){
            $this->db::connectMasterDB();
            $this->db->query("INSERT INTO categories (category_title) 
                              VALUES (:title)");
            $this->db->bind(":title", $title);
            return trueOrFalse($this->db->execute());
        }

        public function getLastCreatedCategory(){
            $this->db::connectMasterDB();
            $lastInsertId = $this->db->lastInsertId();
            $this->db->query("SELECT * FROM categories WHERE category_id = :id");
            $this->db->bind(":id", $lastInsertId);
            $this->db->execute();

            $rowCount = $this->db->rowCount();
            if($rowCount === 0){
                status500("Failed to retreive category after creation");
            }
            return $this->db->single();
        }

        public function getAllCategories(){
            $this->db->query("SELECT * FROM categories 
                              GROUP BY category_id ASC");
            return $this->db->resultSet();
        }

        public function countAllCategories(){
            $this->db->query("SELECT count(*) FROM categories");
            return $this->db->fetchColumn();
        }

        public function getCategoriesPagination($limitPerPage, $offset){
            $this->db->query("SELECT * FROM categories 
                              ORDER BY category_id ASC
                              LIMIT :limit OFFSET :offset");
            $this->db->bind(":limit", $limitPerPage);
            $this->db->bind(":offset", $offset);
            return $this->db->resultSet();
        }

        public function updateCategory($data){
            $this->db::connectMasterDB();
            $this->db->query("UPDATE categories SET category_title = :title 
                              WHERE category_id = :id");
            $this->db->bind(":id", $data["id"]);
            $this->db->bind(":title", $data["title"]);
            $this->db->execute();
        }

        public function getUpdatedCategory($id){
            $this->db::connectMasterDB();
            $this->db->query("SELECT * FROM categories WHERE category_id = :id");
            $this->db->bind(":id", $id);
            return $this->db->single();
        }

        public function deleteCategory($id){
            $this->db->connectMasterDB();
            $this->db->query("DELETE FROM categories WHERE category_id = :id");
            $this->db->bind(":id", $id);
            return trueOrFalse($this->db->execute());
        }
    }