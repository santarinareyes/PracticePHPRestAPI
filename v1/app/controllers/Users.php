<?php 
    class Users extends BaseController {
        public function __construct()
        {
            $this->userModel = $this->model('User');
        }

        public function index($id = ""){
            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                if($_SERVER['CONTENT_TYPE'] !== 'application/json'){
                    status400("Content type header is not set to JSON");
                }

                $inputData = file_get_contents('php://input');

                if(!$jsonData = json_decode($inputData)){
                    status400("Request body is not valid JSON");
                }

                if(!isset($jsonData->firstname) || !isset($jsonData->lastname) || !isset($jsonData->username) || !isset($jsonData->email) || !isset($jsonData->password)){

                    $error_message = [];
                    !isset($jsonData->firstname) ? array_push($error_message, "Firstname cannot be empty") : false;
                    !isset($jsonData->lastname) ? array_push($error_message, "Lastname cannot be empty") : false;
                    !isset($jsonData->username) ? array_push($error_message, "Username cannot be empty") : false;
                    !isset($jsonData->email) ? array_push($error_message, "Email cannot be empty") : false;
                    !isset($jsonData->password) ? array_push($error_message, "Password cannot be empty") : false;
                    
                    status400($error_message);
                }

                if((isset($jsonData->role) && sanitizeString($jsonData->role) != 'Admin') || strlen($jsonData->firstname) < 1 || strlen($jsonData->firstname) > 255 || strlen($jsonData->lastname) < 1 || strlen($jsonData->lastname) > 255 || strlen($jsonData->username) < 1 || strlen($jsonData->username) > 255 || !filter_var($jsonData->email, FILTER_VALIDATE_EMAIL) || strlen($jsonData->email) < 1 || strlen($jsonData->email) > 255 || strlen($jsonData->password) < 1 || strlen($jsonData->password) > 255){

                    $error_message = [];
                    $jsonData->role != 'admin' ? array_push($error_message, "Set role to 'admin' or remove the role row to set to user") : false;
                    strlen($jsonData->firstname) < 1 ? array_push($error_message, "Firstname cannot be blank") : false;
                    strlen($jsonData->firstname) > 255 ? array_push($error_message, "Firstname cannot be greater than 255 characters (Your input: ".strlen($jsonData->firstname)." characters)") : false;
                    strlen($jsonData->lastname) < 1 ? array_push($error_message, "Lastname cannot be blank") : false;
                    strlen($jsonData->lastname) > 255 ? array_push($error_message, "Lastname cannot be greater than 255 characters (Your input: ".strlen($jsonData->lastname)." characters)") : false;
                    str_contains($jsonData->username, " ") ? array_push($error_message, "Username cannot have spaces") : false;
                    strlen($jsonData->username) < 1 ? array_push($error_message, "Username cannot be blank") : false;
                    strlen($jsonData->username) > 255 ? array_push($error_message, "Username cannot be greater than 255 characters (Your input: ".strlen($jsonData->username)." characters)") : false;
                    !filter_var($jsonData->email, FILTER_VALIDATE_EMAIL) ? array_push($error_message, "Email is invalid") : false;
                    str_contains($jsonData->email, " ") ? array_push($error_message, "Email cannot have spaces") : false;
                    strlen($jsonData->email) < 1 ? array_push($error_message, "Email cannot be blank") : false;
                    strlen($jsonData->email) > 255 ? array_push($error_message, "Email cannot be greater than 255 characters (Your input: ".strlen($jsonData->username)." characters)") : false;
                    strlen($jsonData->password) < 1 ? array_push($error_message, "Password cannot be blank") : false;
                    strlen($jsonData->password) > 255 ? array_push($error_message, "Password cannot be greater than 255 characters (Your input: ".strlen($jsonData->password)." characters)") : false;

                    status400($error_message);
                }

                $firstname = sanitizeString($jsonData->firstname);
                $lastname = sanitizeString($jsonData->lastname);
                $username = sanitizeUsername($jsonData->username);
                $email = sanitizeEmail($jsonData->email);
                $password = sanitizePassword($jsonData->password);

                $checkUserExist = $this->userModel->checkUserExist($username, $email);

                if(!empty($checkUserExist)){
                    $existArray = [];

                    if(isset($checkUserExist->email) && $checkUserExist->email == $email){
                        array_push($existArray, "Email already exist");
                    }

                    if(isset($checkUserExist->username) && $checkUserExist->username == $username){
                        array_push($existArray, "Username already exist");
                    }

                    status409($existArray);
                }

                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $data = [
                    "firstname" => ucwords(strtolower($firstname)),
                    "lastname" => ucwords(strtolower($lastname)),
                    "username" => $username,
                    "email" => $email,
                    "password" => $hashed_password,
                    "role" => isset($jsonData->role) ? sanitizeString($jsonData->role) : "User"
                ];

                $newUser = $this->userModel->createUser($data);

                if($newUser){
                    $latestInfo = $this->userModel->getLastCreatedUser();
                    $rows = count(array($latestInfo));

                    try{
                        $user = new UserValidator($latestInfo->user_id, $latestInfo->firstname, $latestInfo->lastname, $latestInfo->username, $latestInfo->email, $latestInfo->role);
                        $array[] = $user->returnAsArray();
                        $array['data'] = "users";
                        $array['message'] = "User created";
                        
                        $returnData = returnData($rows, $array);
                        status200($returnData, $array['data']);
                        
                    } catch(UserException $e){
                        status500($e);
                    } 

                } else {
                    status500("There was an issue creating a user account. Please try again.");
                }

            } elseif($_SERVER['REQUEST_METHOD'] === 'GET'){
                if($id == ""){
                    $users = $this->userModel->getAllUsers();
                    $rows = count($users);
                    
                    foreach($users as $user){
                        $user = new UserValidator($user->user_id, $user->firstname, $user->lastname, $user->username, $user->email, $user->role);
                        $array[] = $user->returnAsArray();
                    }

                    $array['data'] = "users";
                    $returnData = returnData($rows, $array);
                    status200($returnData, false, true);
                }

                if($id == "admin"){
                    $users = $this->userModel->getRoleAdmin($id);
                    $rows = count($users);
                    
                    foreach($users as $user){
                        $user = new UserValidator($user->user_id, $user->firstname, $user->lastname, $user->username, $user->email, $user->role);
                        $array[] = $user->returnAsArray();
                    }

                    $array['data'] = "users";
                    $returnData = returnData($rows, $array);
                    status200($returnData, false, true);
                }

                if($id == "user"){
                    $users = $this->userModel->getRoleUser($id);
                    $rows = count($users);
                    
                    foreach($users as $user){
                        $user = new UserValidator($user->user_id, $user->firstname, $user->lastname, $user->username, $user->email, $user->role);
                        $array[] = $user->returnAsArray();
                    }

                    $array['data'] = "users";
                    $returnData = returnData($rows, $array);
                    status200($returnData, false, true);
                }

                if(!is_numeric($id)){
                    status400("Category ID must be numeric");
                }

                $singleUser = $this->userModel->getSingleUser($id);
                $rows = count(array($singleUser));

                if(!empty($singleUser)){
                    try{
                        $user = new UserValidator($singleUser->user_id, $singleUser->firstname, $singleUser->lastname, $singleUser->username, $singleUser->email, $singleUser->role);
                        $array[] = $user->returnAsArray();
                        $array['data'] = "users";
                        
                        $returnData = returnData($rows, $array);
                        status200($returnData);
                        
                    } catch(UserException $e){
                        status500($e);
                    } 
                } else {
                    status404("User not found");
                }

            } else {
                status405("Request method not allowed");
            }
        }
    }