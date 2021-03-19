<?php 
    class Category {
        private $db;

        public function __construct()
        {
            $this->db = new Database;
            $this->db::connectReadDB();
        }

        public function getSingleCategory($title){
            $this->db->query("SELECT category_title FROM categories WHERE category_title = :title");
            $this->db->bind(":title", $title);
            return $this->db->fetchColumn();
        }

        public function createCategory($title){
            $this->db->query("INSERT INTO categories (category_title) 
                              VALUES (:title)");
            $this->db->bind(":title", $title);
            return trueOrFalse($this->db->execute());
        }

        public function getAllCategories(){
            $this->db->query("SELECT * FROM categories");
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
    }