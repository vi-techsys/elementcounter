<?php
include('class.webElement.php');
header('Content-Type: application/json');
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
$response = array('element'=>'','length'=>0, 'message'=>'');
if ($contentType === "application/json") {
    //Receive the RAW post data.
    $content = trim(file_get_contents("php://input"));
    $decodedContent = json_decode($content, true);
    $url = isset($decodedContent['url'])?$decodedContent['url']:'';
    $element = isset($decodedContent['element'])?$decodedContent['element']:'';
    if(empty($url) || is_null($url)||empty($element)||is_null($element)){
        http_response_code(400);
        $response['message'] = 'One of the required values is not supplied';
    }
    else{
            $webElement = new webElement();
            $webElement->url = $url;
            $webElement->element = $element;
            $count = $webElement->countElement();
            if($count === -1){
                http_response_code(503);
                $response['message'] = 'The domain is down or not accessible';  
            }
            else if($count === -2){
                http_response_code(404);
                $response['message'] = 'The domain does not return a valid html';
            }
            else if($count === -3){
                http_response_code(400);
                $response['message'] = "Invalid URL";
            }
            else{
                $response['element'] = $element;
                $response['length'] = $count;
                $response['message'] = 'The url is accessible';
            }
        }
}
else{
    http_response_code(400);
    $response['message'] = 'Bad Request';
}
echo json_encode($response);