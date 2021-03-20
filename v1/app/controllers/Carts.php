<?php 
    class Carts extends BaseController {
        private $singular = "Cart";
        private $plural = "carts";

        public function __construct()
        {
            $this->cartModel = $this->model($this->singular);
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

                if(!isset($jsonData->username) || !isset($jsonData->product)){

                    $error_message = [];
                    !isset($jsonData->username) ? array_push($error_message, "Username cannot be empty") : false;
                    !isset($jsonData->product) ? array_push($error_message, "Product cannot be empty") : false;
                    
                    status400($error_message);
                }

                if(strlen($jsonData->username) < 1 || strlen($jsonData->username) > 255 || strlen($jsonData->product) < 1 || strlen($jsonData->product) > 255){

                    $error_message = [];
                    strlen($jsonData->username) < 1 ? array_push($error_message, "User cannot be blank") : false;
                    strlen($jsonData->username) > 255 ? array_push($error_message, "User cannot be greater than 255 characters (Your input: ".strlen($jsonData->username)." characters)") : false;
                    strlen($jsonData->product) < 1 ? array_push($error_message, "Product cannot be blank") : false;
                    strlen($jsonData->product) > 255 ? array_push($error_message, "Product cannot be greater than 255 characters (Your input: ".strlen($jsonData->product)." characters)") : false;

                    status400($error_message);
                }

                $userId = "";
                $productId = "";

                $error_message = [];
                $checkUsername = $this->cartModel->checkUsername(sanitizeString($jsonData->username));
                empty($checkUsername) ? array_push($error_message, "Username does not exist. Please try again.") : false;
                
                $checkProduct = $this->cartModel->checkProduct(sanitizeString($jsonData->product));
                empty($checkProduct) ? array_push($error_message, "Product does not exist. Please try again.") : false;
                empty($checkUsername) || empty($checkProduct) ? status400($error_message) : false;

                $data = [
                    "user" => $checkUsername->user_id,
                    "product" => $checkProduct->product_id
                ];

                $addCartItem = $this->cartModel->addCartItem($data);

                if($addCartItem){
                    $latestInfo = $this->cartModel->getLastAddedCartItem();
                    $rows = count(array($latestInfo));

                    try{
                        $cart = new CartValidator($latestInfo->cart_id, $latestInfo->username, $latestInfo->product_title);
                        $array[] = $cart->returnAsArray();
                        $array['data'] = $this->plural;
                        $array['message'] = "$this->singular item added";
                        
                        $returnData = returnData($rows, $array);
                        status201($returnData, $array['data']);
                        
                    } catch(CartException $e){
                        status500($e);
                    } 

                } else {
                    status500("There was an issue creating a new $this->singular. Please try again.");
                }

            } elseif($_SERVER['REQUEST_METHOD'] === 'GET'){
                if($id == ""){
                    $carts = $this->cartModel->getAllCartItems();
                    $rows = count($carts);
                    
                    foreach($carts as $cart){
                        $cart = new CartValidator($cart->cart_id, $cart->username, $cart->product_title);
                        $array[] = $cart->returnAsArray();
                    }

                    $array['data'] = $this->plural;
                    $returnData = returnData($rows, $array);
                    status200($returnData, false, true);
                }

                if($id == "total"){
                    $cartTotals = $this->cartModel->getAllCartTotals();
                    $rows = count($cartTotals);

                    
                    foreach($cartTotals as $cartTotal){
                        $cart = new CartValidator($cartTotal->user_id, $cartTotal->username, $cartTotal->total);
                        $array[] = $cart->returnAsArrayTotal();
                    }
                    
                    $array['data'] = $this->plural;
                    $returnData = returnData($rows, $array);
                    status200($returnData, false, true);
                }

                if(!is_numeric($id)){
                    $checkUsername = $this->cartModel->checkUsername($id);
                    empty($checkUsername) ? status400("Username does not exist. Please try again.") : false;

                    $userTotal = $this->cartModel->getUserCartTotal($id);
                    $rows = count(array($userTotal));
                    
                    if($userTotal){
                        try{
                            $cart = new CartValidator($userTotal->user_id, $userTotal->username, $userTotal->total);
                            $array[] = $cart->returnAsArrayTotal();
                            
                            $array['data'] = $this->plural;
                            $returnData = returnData($rows, $array);
                            status200($returnData, false, true);
                        } catch(CartException $e){
                            status500($e);
                        }
                    } else {
                        status404("This user does not have an existing cart");
                    }
                }

                if(is_numeric($id)){
                    $checkUsername = $this->cartModel->checkUserId($id);
                    empty($checkUsername) ? status400("User Id does not exist. Please try again.") : false;

                    $userTotal = $this->cartModel->getIdCartTotal($id);
                    $rows = count(array($userTotal));
                    
                    if($userTotal){
                        try{
                            $cart = new CartValidator($userTotal->user_id, $userTotal->username, $userTotal->total);
                            $array[] = $cart->returnAsArrayTotal();
                            
                            $array['data'] = $this->plural;
                            $returnData = returnData($rows, $array);
                            status200($returnData, false, true);
                        } catch(CartException $e){
                            status500($e);
                        }
                    } else {
                        status404("This user does not have an existing cart");
                    }
                }

            } elseif($_SERVER['REQUEST_METHOD'] === 'DELETE'){
                if($id === ""){
                    status404("Please specify an Id or Username");
                }

                if(!is_numeric($id)){
                    $checkUsername = $this->cartModel->checkUsername($id);
                    empty($checkUsername) ? status400("Username does not exist. Please try again.") : false;
                    $userTotal = $this->cartModel->getUserCartTotal($id);
                    
                    if($userTotal){
                        $deleteUserCart = $this->cartModel->deleteUserCart($userTotal->user_id);
                        $rows = $deleteUserCart === true ? 1 : status500("Failed to delete $this->singular");
                        
                        try{
                            $cart = new CartValidator($userTotal->user_id, $userTotal->username, $userTotal->total);
                            $array[] = $cart->returnAsArrayTotal();
                            $array['data'] = $this->plural;
                            $array['message'] = "$this->singular deleted";
                            
                            $returnData = returnData($rows, $array);
                            status200($returnData, $array['data']);
                        } catch(CartException $e){
                            status500($e);
                        }
                    } else {
                        status404("This user does not have an existing cart");
                    }
                }

                $checkCartItem = $this->cartModel->checkCartItem($id);
                empty($checkCartItem) ? status400("Cart item does not exist. Please try again.") : false;
                
                if($checkCartItem){
                    $deleteUserCart = $this->cartModel->deleteCartItem($id);
                    $rows = $deleteUserCart === true ? 1 : status500("Failed to delete $this->singular");
                    
                    try{
                        $cart = new CartValidator($checkCartItem->cart_id, $checkCartItem->username, $checkCartItem->product_title);
                        $array[] = $cart->returnAsArray();
                        $array['data'] = $this->plural;
                        $array['message'] = "$this->singular item deleted";
                        
                        $returnData = returnData($rows, $array);
                        status200($returnData, $array['data']);
                    } catch(CartException $e){
                        status500($e);
                    }
                } else {
                    status404("This user does not have an existing cart");
                }

            } else {
                status405("Request method not allowed");
            }
        }

        public function page($currentPage = ""){
            if($_SERVER['REQUEST_METHOD'] === 'GET'){
                if($currentPage == "" || !is_numeric($currentPage)){
                    status400("Page cannot be empty or must be numeric");
                }

                if($currentPage == 0){
                    $currentPage = 1;
                }

                $limitPerPage = 20;
                $numRows = intval($this->cartModel->countAllCarts());
                $numPages = ceil($numRows/$limitPerPage);

                if($numPages == 0 || $numPages == ""){
                    $numPages = 1;
                }

                if($currentPage > $numPages){
                    status404("Page not found");
                }

                $offset = ($currentPage == 1 ? 0 : ($limitPerPage*($currentPage-1)));
                $rows = $this->cartModel->getCartsPagination($limitPerPage, $offset);
                $pageRows = count($rows);

                foreach($rows as $row){
                    $row = new CartValidator($row->user_id, $row->username, $row->total);
                    $array[] = $row->returnAsArrayTotal();
                    $array['data'] = $this->plural;
                }

                $hasNextPage = $currentPage < $numPages;
                $hasPrevPage = $currentPage == 1 ? true : $currentPage > $numPages;

                $returnData = returnPageData($numRows, $pageRows, $numPages, $hasNextPage, $hasPrevPage, $array);
                status200($returnData, true);

            } else {
                status405("Request method not allowed");
            }
        }
    }