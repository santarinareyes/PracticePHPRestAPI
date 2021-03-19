<?php 
    function returnData($rows, $array) {
        if(!empty($rows)){
            $returnData['rows_returned'] = $rows;
        }
        
        $returnData['tasks'] = $array;
        return $returnData;
    }

    function returnPageData($rows, $pageRows, $pages, $hasNextPage, $hasPrevPage, $array) {
        $returnData['total_rows'] = $rows;
        $returnData['current_page_rows'] = $pageRows;
        $returnData['total_pages'] = $pages;

        $hasNextPage === true ? $returnData['has_next_page'] = true : $returnData['has_next_page'] = false;
        $hasPrevPage === true ? $returnData['hasPrevPage'] = false : $returnData['hasPrevPage'] = true;

        $returnData['tasks'] = $array;
        return $returnData;
    }