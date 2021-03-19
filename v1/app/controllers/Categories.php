<?php 
    class Categories extends BaseController {
        public function __construct()
        {
            $this->categoryModel = $this->model('Category');
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
                
                if(!isset($jsonData->title)){
                    $error_message = [];
                    !isset($jsonData->title) ? array_push($error_message, "Title cannot be empty") : false;
                    status400($error_message);
                }
                
                if(strlen($jsonData->title) < 1 || strlen($jsonData->title) > 20){
                    $error_message = [];
                    strlen($jsonData->title) < 1 ? array_push($error_message, "Title cannot be blank") : false;
                    strlen($jsonData->title) > 20 ? array_push($error_message, "Title cannot be greater than 20 characters", "Your input: ".strlen($jsonData->title)." characters") : false;
                    status400($error_message);
                }

                $title = sanitizeString($jsonData->title);
                $checkCategoryExist = $this->categoryModel->getSingleCategory($title);

                if(!empty($checkCategoryExist)){
                    $existArray = [];
                    if($checkCategoryExist == $title){
                        array_push($existArray, "Category title already exist");
                    }
                    status409($existArray);
                }

                $title = ucwords(strtolower($title));
                $newCategory = $this->categoryModel->createCategory($title);

                if($newCategory){
                    $newData = [
                        "data" => "categories",
                        "message" => "Category created",
                        "title" => ucwords(strtolower($title))
                    ];

                        $returnData = returnData("", $newData);
                        status201($returnData, $newData["data"]);

                } else {
                    status500("There was an issue creating a user account. Please try again.");
                }

            } elseif($_SERVER['REQUEST_METHOD'] === 'GET'){
                if($id == ""){
                    $categories = $this->categoryModel->getAllCategories();
                    $rows = count($categories);
                    
                    foreach($categories as $category){
                        $category = new CategoryValidator($category->category_id, $category->category_title);
                        $array[] = $category->returnAsArray();
                    }

                    $array['data'] = "categories";
                    $returnData = returnData($rows, $array);
                    status200($returnData, false, true);
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

                $limitPerPage = 20;
                $numRows = intval($this->categoryModel->countAllCategories());
                $numPages = ceil($numRows/$limitPerPage);

                if($currentPage == 0 || $currentPage == "" || $numPages == 0 || $numPages == ""){
                    $numPages = 1;
                }

                if($currentPage > $numPages){
                    status404("Page not found");
                }

                $offset = ($currentPage == 1 ? 0 : ($limitPerPage*($currentPage-1)));
                $categories = $this->categoryModel->getCategoriesPagination($limitPerPage, $offset);
                $pageRows = count($categories);

                foreach($categories as $category){
                    $category = new CategoryValidator($category->category_id, $category->category_title);
                    $array[] = $category->returnAsArray();
                }

                $hasNextPage = ($currentPage < $numPages);
                $hasPrevPage = ($currentPage > $numPages);
                
                $returnData = returnPageData($numRows, $pageRows, $numPages, $hasNextPage, $hasPrevPage, $array);
                status200($returnData, true);

            } else {
                status405("Request method not allowed");
            }
        }
    }