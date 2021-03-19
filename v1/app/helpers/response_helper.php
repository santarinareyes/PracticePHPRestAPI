<?php
    /*
     * These functions create a new Response class from Response.php in libs
     * and build up the messages displayed
     */
    function PDOException($e, $message) {
        error_log("PDO Error: ".$e, 0);
        $response = new Response;
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage($message);
        $response->send();
        exit;
    }

    function status200($returnData, $cache = false){
        $response = new Response;
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);

        if($cache){
            $response->setCache(true);
        }
        
        if(isset($returnData["tasks"]["message"])){
            $response->addMessage($returnData["tasks"]["message"]);
            unset($returnData["tasks"]["message"]);
        }
        
        if(is_array($returnData)){
            $response->setData($returnData);

        } else {
            $response->addMessage($returnData);
        }

        $response->send();
        exit;
    }

    function status201($message, $returnData){
        $response = new Response;
        $response->setHttpStatusCode(201);
        $response->setSuccess(true);
        $response->addMessage($message);
        $response->setData($returnData);
        $response->send();
        exit;
    }

    function status400($e){
        $response = new Response;
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);

        if(is_array($e)){
            $response->addMessage($e);
        }elseif(is_string($e)){
            $response->addMessage($e);
        } else {
            $response->addMessage($e->getMessage());
        }

        $response->send();
        exit;
    }
    
    function status404($message){
        $response = new Response;
        $response->setHttpStatusCode(404);
        $response->setSuccess(false);
        $response->addMessage($message);
        $response->send();
        exit;
    }

    function status405($message){
        $response = new Response;
        $response->setHttpStatusCode(405);
        $response->setSuccess(false);
        $response->addMessage($message);
        $response->send();
        exit;
    }

    function status409($e){
        $response = new Response;
        $response->setHttpStatusCode(409);
        $response->setSuccess(false);

        if(is_array($e)){
            $response->addMessage($e);
        }elseif(is_string($e)){
            $response->addMessage($e);
        } else {
            $response->addMessage($e->getMessage());
        }

        $response->send();
        exit;
    }

    function status500($e){
        $response = new Response;
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);

        if(is_array($e)){
            $response->addMessage($e);
        }elseif(is_string($e)){
            $response->addMessage($e);
        } else {
            $response->addMessage($e->getMessage());
        }
        
        $response->send();
        exit;
    }