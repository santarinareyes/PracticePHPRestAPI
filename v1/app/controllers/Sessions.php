<?php 
    class Sessions extends BaseController {
        private $singular = "Session";
        private $plural = "sessions";

        public function __construct()
        {
            $this->sessionModel = $this->model($this->singular);
            $this->userModel = $this->model('User');
        }

        public function index($id = ""){
            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                sleep(1);
                if($_SERVER['CONTENT_TYPE'] !== 'application/json'){
                    status400("Content type header is not set to JSON");
                }

                $inputData = file_get_contents('php://input');

                if(!$jsonData = json_decode($inputData)){
                    status400("Request body is not valid JSON");
                }

                if(!isset($jsonData->username) || !isset($jsonData->password)){

                    $error_message = [];
                    !isset($jsonData->username) ? array_push($error_message, "Username cannot be empty") : false;
                    !isset($jsonData->password) ? array_push($error_message, "Password cannot be empty") : false;
                    
                    status400($error_message);
                }

                if(strlen($jsonData->username) < 1 || strlen($jsonData->username) > 255 || strlen($jsonData->password) < 1 || strlen($jsonData->password) > 255){

                    $error_message = [];
                    strlen($jsonData->username) < 1 ? array_push($error_message, "Username cannot be blank") : false;
                    strlen($jsonData->username) > 255 ? array_push($error_message, "Username cannot be greater than 255 characters (Your input: ".strlen($jsonData->username)." characters)") : false;
                    strlen($jsonData->password) < 1 ? array_push($error_message, "Password cannot be blank") : false;
                    strlen($jsonData->password) > 255 ? array_push($error_message, "Password cannot be greater than 255 characters (Your input: ".strlen($jsonData->password)." characters)") : false;

                    status400($error_message);
                }

                $checkUserExist = $this->userModel->checkUserExist(trim($jsonData->username), "");
                empty($checkUserExist) ? status401("Username or password is incorrect") : false;
                
                $inputPassword = $jsonData->password;

                $user_id = $checkUserExist->user_id;
                $username = $checkUserExist->username;
                $firstname = $checkUserExist->firstname;
                $lastname = $checkUserExist->lastname;
                $email = $checkUserExist->email;
                $password = $checkUserExist->password;
                $isactive = $checkUserExist->isactive;
                $loginattempts = $checkUserExist->loginattempts;

                $isactive !== "Y" ? status401("User account is locked") : false;
                $loginattempts >= 3 ? status401("User account has been locked") : false;

                if(!password_verify($inputPassword, $password)){
                    $this->sessionModel->updateLoginAttempts($user_id);
                    status401("Username or password is incorrect");
                }
            } else {
                status405("Request method not allowed");
            }
        }
    }