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
                $checkCategoryExist = $this->categoryModel->checkCategoryExist($title);
                !empty($checkCategoryExist) ? status409("Category title already exist") : false;

                $title = ucwords(strtolower($title));
                $newCategory = $this->categoryModel->createCategory($title);

                if($newCategory){
                    $latestInfo = $this->categoryModel->getLastCreatedCategory();
                    $rows = count(array($latestInfo));
                        
                        if(!empty($latestInfo)){
                            try{
                                $category = new CategoryValidator($latestInfo->category_id, $latestInfo->category_title);
                                $array[] = $category->returnAsArray();
                                $array['data'] = "categories";
                                $array['message'] = "Category created";
                                
                                $returnData = returnData($rows, $array);
                                status200($returnData, $array['data']);
                                
                            } catch(CategoryException $e){
                                status500($e);
                            } 
                        } else {
                            status404("Category not found");
                        }

                    $newData = [
                        "data" => "categories",
                        "message" => "Category created",
                        "title" => ucwords(strtolower($title))
                    ];

                        $returnData = returnData("", $newData);
                        status201($returnData, $newData["data"]);

                } else {
                    status500("There was an issue creating a new category. Please try again.");
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

                if(!is_numeric($id)){
                    status400("Category ID must be numeric");
                }

                $category = $this->categoryModel->getSingleCategory($id);
                $rows = count(array($category));

                if(!empty($category)){
                    try{
                        $category = new CategoryValidator($category->category_id, $category->category_title);
                        $array[] = $category->returnAsArray();

                        $array['data'] = "categories";
                        $returnData = returnData($rows, $array);
                        status200($returnData, true);
                        
                    } catch(CategoryException $e){
                        status500($e);
                    } 
                } else {
                    status404("Category not found");
                }

            } elseif($_SERVER['REQUEST_METHOD'] === 'PATCH'){
                if($id === "" || !is_numeric($id)){
                    status400("Category ID cannot be empty and must be numeric");
                }

                if($_SERVER['CONTENT_TYPE'] !== 'application/json'){
                    status400("Content type header is not set to JSON");
                }

                $updateData = file_get_contents('php://input');

                if(!$jsonData = json_decode($updateData)){
                    status400("Request body is not valid JSON");
                }
                
                if(isset($jsonData->title) && (strlen($jsonData->title) < 1 || strlen($jsonData->title) > 20)){
                    $error_message = [];
                    strlen($jsonData->title) < 1 ? array_push($error_message, "Title cannot be blank") : false;
                    strlen($jsonData->title) > 20 ? array_push($error_message, "Title cannot be greater than 20 characters", "Your input: ".strlen($jsonData->title)." characters") : false;
                    status400($error_message);
                }
                
                $category = $this->categoryModel->getSingleCategory($id);
                
                if(!empty($category)):
                    !isset($jsonData->title) ? status400("Write a new Title to update the title") : false;
                    sanitizeString($jsonData->title);
                    $checkCategoryExist = $this->categoryModel->checkCategoryExist($jsonData->title);
                    !empty($checkCategoryExist) ? status409("Category title already exist") : false;

                    try{
                        $category = new CategoryValidator($category->category_id, $category->category_title);
                        $category->setTitle($jsonData->title);
                        $newTitle = $category->getTitle();
                        
                        $data = [
                            "id" => $id,
                            "title" => ucwords(strtolower($newTitle))
                        ];
                            
                        $this->categoryModel->updateCategory($data);
                        $updateInfo = $this->categoryModel->getUpdatedCategory($id);
                        $rows = count(array($updateInfo));
                        
                        if(!empty($updateInfo)){
                            try{
                                $category = new CategoryValidator($updateInfo->category_id, $updateInfo->category_title);
                                $array[] = $category->returnAsArray();
                                $array['data'] = "categories";
                                $array['message'] = "Category updated";
                                
                                $returnData = returnData($rows, $array);
                                status200($returnData, $array['data']);
                                
                            } catch(CategoryException $e){
                                status500($e);
                            } 
                        } else {
                            status404("Category not found");
                        }
                        
                    } catch(CategoryException $e){
                        status400($e);
                    } 
                else:
                    status404("Category not found");
                endif;
                
            } elseif($_SERVER['REQUEST_METHOD'] === 'DELETE'){
                if($id === ""){
                    status404("No category found to delete");
                }

                if(!is_numeric($id)){
                    status400("Category Id must be numeric");
                }

                $categoryToDelete = $this->categoryModel->getSingleCategory($id);
                empty($categoryToDelete) ? status404("Category not found") : false;
                $category = $this->categoryModel->deleteCategory($id);
                $rows = $category === true ? 1 : status500("Failed to delete category");

                if($category):
                    try{
                        $category = new CategoryValidator($categoryToDelete->category_id, $categoryToDelete->category_title);
                        $array[] = $category->returnAsArray();
                        $array['data'] = "categories";
                        $array['message'] = "Category deleted";
                        
                        $returnData = returnData($rows, $array);
                        status200($returnData, $array['data']);
                        
                    } catch(CategoryException $e){
                        status500($e);
                    } 
                else:
                    status404("Category not found");
                endif;

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