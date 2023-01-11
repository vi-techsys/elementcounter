<?php
//turn off error and warnings messages
//error_reporting(E_ERROR | E_PARSE);
header('Content-Type: application/json');
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    $response = array('element'=>'','length'=>0, 'message'=>'');
if ($contentType === "application/json") {
  //Receive the RAW post data.
  $content = trim(file_get_contents("php://input"));
  $decodedContent = json_decode($content, true);
    $url =isset($decodedContent['url'])?$decodedContent['url']:'';
    $element = isset($decodedContent['element'])?$decodedContent['element']:'';
    //check if url is valid
    if (preg_match("/\b(?:(?:https?|http):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$url)) {
      try
      {
        $htmlResponse = @file_get_contents($url);
        //check if response is empty
        if(empty($htmlResponse)){
          http_response_code(503);
          $response['message'] = 'The url is down or not accessible';  
        }
        else
        {
          $dom = new DOMDocument();
          if($dom->loadHTML($htmlResponse))
          {
            $imgs = $dom->getElementsByTagName($element);
            $response['element'] = $element;
            $response['length'] = count($imgs);
          }
          else{
            http_response_code(404);
            $response['message'] = 'The url does not return a valid html';
          }
        }
      }
      catch(Exception $ex){
        http_response_code(404);
        $response['message'] = 'The url does not return a valid html';
      }
    }
    else
    {
      http_response_code(400);
      $response['message'] = "Invalid URL";
    }
}
else
{
  http_response_code(400);
  $response['element'] = '';
  $response['length'] = 0;
  $response['message'] ='Bad request';
}
echo json_encode($response);
?>