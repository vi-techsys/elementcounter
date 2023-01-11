<?php
//turn off error and warnings messages
error_reporting(E_ERROR | E_PARSE);
include("class.crud.php");
class webElement extends DOMDocument{
    public $url='';
    public $element=0;
    /*
        Return Codes
        -1 = The url is down or not accessible
        -2 = The url does not return a valid html
        -3 = Invalid URL or Element
    */
    function getUrlContent($url) {
        $parts = parse_url($url);
        $host = $parts['host'];
        $ch = curl_init();
        $header = array('GET /1575051 HTTP/1.1',
            "Host: {$host}",
            'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language:en-US,en;q=0.8',
            'Cache-Control:max-age=0',
            'Connection:keep-alive',
            'Host:adfoc.us',
            'User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.116 Safari/537.36',
        );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    public function countElement(){
        //check if $element is not empty
        if(empty($this->element) || is_null($this->element)){
            return -3;
        }
        //try
        //{
            //check if url is valid
            if (preg_match("/\b(?:(?:https?|http):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$this->url)) {
                $crud = new CRUD();
                $domain = explode('/',$this->url)[2];
                //check if domain already exists in db
                $domainTable = 'domain';
                $search_conditions = array( 
                    'select' => 'id',
                    'where' => array( 
                        'name' =>$domain 
                    ), 
                    'return_type' => 'single'
                );
                $domainRecord = $crud->getRecordsFromTable($search_conditions, $domainTable);
                if(!isset($domainRecord['id'])){
                //save domain
                $data=array( 
                    'name' => $domain, 
                    );  
                $domainRecord['id'] = $crud->insertRecord($data,$domainTable);
                }
                //check if url already exists
                $urlTable = 'url';
                $search_conditions = array( 
                    'select' => 'id',
                    'where' => array( 
                        'name' =>$this->url
                    ), 
                    'return_type' => 'single'
                );
                $urlRecord = $crud->getRecordsFromTable($search_conditions, $urlTable);
                if(!isset($urlRecord['id'])){
                //save url
                $data=array( 
                    'name' => $this->url, 
                    );  
                $urlRecord['id'] = $crud->insertRecord($data,$urlTable);
                }
                //check if element already exists
                $elementTable = 'element';
                $search_conditions = array( 
                    'select' => 'id',
                    'where' => array( 
                        'name' =>$this->element
                    ), 
                    'return_type' => 'single'
                );
                $elementRecord = $crud->getRecordsFromTable($search_conditions, $elementTable);
                if(!isset($elementRecord['id'])){
                //save element
                $data=array( 
                    'name' => $this->element, 
                    );  
                $elementRecord['id'] = $crud->insertRecord($data,$elementTable);
                }
                //check if same request was made less than 5 minutes ago
                $requestTable = 'request';
                $requestRecord = $crud->customSelect('SELECT count FROM `request` WHERE domain_id = '.addslashes($domainRecord['id']).' AND url_id = '.addslashes($urlRecord['id']).' AND element_id = '.$elementRecord['id'].' AND TIMESTAMPDIFF(MINUTE,time,NOW()) < 5', 'single');
                if(isset($requestRecord['count'])){
                    $count = $requestRecord['count'];
                    return $count;
                }
                else{
                    $milliseconds1 = floor(microtime(true) * 1000);
                    //get page content as string
                    //$htmlResponse = file_get_contents($this->url);
                    $htmlResponse = $this->getUrlContent($this->url);
                    $duration = floor(microtime(true) * 1000) - $milliseconds1;
                     //check if response is empty
                     if(empty($htmlResponse)){
                      return -1;
                     }
                     else
                     {
                       if($this->loadHTML($htmlResponse))
                       {
                         $count = count($this->getElementsByTagName($this->element));
                        //save user request
                        $data=array( 
                            'domain_id' => $domainRecord['id'],
                            'url_id' => $urlRecord['id'],
                            'element_id' => $elementRecord['id'], 
                            'count' => $count,
                            'duration' => $duration
                            );  
                        $requestRecord['id'] = $crud->insertRecord($data,$requestTable);
                        return $count;
                       }
                       else{
                        return -2;
                       }
                    }
                }
            }
            else
            {
                return -3;
            }
        //}   
        //catch(Exception $ex){
          //  return -2;
        //} 
    }
}
