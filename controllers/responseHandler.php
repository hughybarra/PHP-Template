<?php
/* controller stored in controllers/csv_upload.php */
class ResponseHandler{

    /*
    * json_error_response
    * returns an error response
    */
    public static function json_response($message = null, $code = 200, $data = null)
    {
        // clear the old headers
        header_remove();
        // set the actual code
        http_response_code($code);
        // set the header to make sure cache is forced
        header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
        // treat this as json
        header('Content-Type: application/json');

        $status = array(
            200 => '200 OK',
            400 => '400 Bad Request',
            422 => 'Unprocessable Entity',
            500 => '500 Internal Server Error'
        );

        // check for response type 
        if($code < 300){
            $response = 'success';
        }else{
            $response = 'error';
        }
        // ok, validation error, or failure
        header('Status: '.$status[$code]);
        // if we have data return data with the array 
        $responseData = array(
            'status' => $response, // success or not?
            'message' => $message, 
            'data' => $data
        );   

        // return the encoded json
        echo json_encode($responseData);
    }
}