<?php 
    class TaskException extends Exception {}

    class TaskVerifier {
        private $_id;
        private $_title;
        private $_description;
        private $_deadline;
        private $_completed;

        public function __construct($id, $title, $description, $deadline, $completed)
        {
            $this->setId($id);
            $this->setTitle($title);
            $this->setDescription($description);
            $this->setDeadline($deadline);
            $this->setCompleted($completed);
        }

        public function getId(){
            return $this->_id;
        }
        
        public function getTitle(){
            return $this->_title;
        }
        
        public function getDescription(){
            return $this->_description;
        }
        
        public function getDeadline(){
            return $this->_deadline;
        }
        
        public function getCompleted(){
            return $this->_completed;
        }

        public function setId($id){
            if(($id !== null) && (!is_numeric($id) || $id <= 0 || $id > 9223372036854775807 || $this->_id !== null)){
                throw new TaskException('Task Id error');
            }

            $this->_id = $id;
        }

        public function setTitle($title){
            if(strlen($title) < 0 || strlen($title) > 255){
                throw new TaskException('Task title error');
            }

            $this->_title = $title;
        }

        public function setDescription($description){
            $this->_description = $description;
        }

        public function setDeadline($deadline){
            $this->_deadline = $deadline;
        }

        public function setCompleted($completed){
            if(strtoupper($completed) !== 'Y' && strtoupper($completed) !== 'N'){
                throw new TaskException('Task completed must be Y or N');
            }

            $this->_completed = $completed;
        }

        public function returnTaskAsArray(){
            $task = [];
            $task['id'] = $this->getId();
            $task['title'] = $this->getTitle();
            $task['description'] = $this->getDescription();
            $task['deadline'] = $this->getDeadline();
            $task['completed'] = $this->getCompleted();
            return $task;
        }
    }