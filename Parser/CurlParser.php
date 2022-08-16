<?php

class CurlParser
{
  public $doc;
  public $xpath;
  public $headers;
  public function __construct()
  {
    $this->headers=array('cache-control: max-age=0',
    'upgrade-insecure-requests: 1',
    'user-agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36',
    'sec-fetch-user: ?1',
    'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
    'x-compress: null',
    'sec-fetch-site: none',
    'sec-fetch-mode: navigate',
    'accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7');
    $this->doc = new DOMDocument;
    $this->xpath = new DOMXPath($this->doc);
  }
  
  function curl_connect($url)
  {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 400);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $html = curl_exec($ch);
    curl_close($ch);

    //load $html into DOMDocument $doc without warnings and errors
  
    $this->doc->loadHTML($html,LIBXML_NOWARNING | LIBXML_NOERROR);
    // $this->doc->loadHTML($html);

    // $this->doc->saveHTMLFile(__DIR__ ."/HTML");
    
    //rewrite $xpath with new DOMXpath of $doc
    $this->xpath = new DOMXPath($this->doc);
    //echo var_dump($this->xpath);
    return $this->xpath;
  }

  function txtNodes_to_array($nodeList){
    $array=array();
    foreach ($nodeList as $node) { 
    //  the first childNode is whether #text, or a tags
      array_push($array,trim($node->childNodes[0]->nodeValue)); 
    }
    return $array;
  }
  function attrNodes_to_array($nodeList,$attribute="href",$urlPrefix=""){
    $array=array();
    foreach ($nodeList as $node) { 
    //  the first childNode is whether #text, or a tags
      array_push($array,($urlPrefix.trim($node->getAttribute($attribute)))); 
    }
    return $array;
  }




  function get_urls($URL,$path,$logTabulation=0)
  {
    $xpath=$this->curl_connect($URL);
    //sleep avoiding block
    sleep(rand(1,2));
    
    $nodesA = $xpath->query($path);
    $arrA = $this->attrNodes_to_array($nodesA,'href','https://www.auto-data.net');
    //get current time
    $time = new DateTimeImmutable();
    // time +"\n" + formatted arr 
    $txt = $time->format("[H:i:s.u]")."\n".print_r($arrA,true);
   
   
    $logFormattedTxt = str_replace("\n","\n".str_repeat(" ",$logTabulation),$txt);
    file_put_contents(__DIR__ ."/logs.txt",$logFormattedTxt, FILE_APPEND);

    // file_put_contents(__DIR__ ."/logs.txt",$txt, FILE_APPEND);
    return $arrA;
  }
  









  function get_car($URL)
  {
    $xpath=$this->curl_connect($URL);
    //sleep avoiding block
    sleep(rand(1,2));
    
    //tr which class is not @class and have td child 
    $nodesTh = $xpath->query("/html/body//div[@id='outer']/table/tr[not (@class) and (./td)]/th");
    $nodesTd = $xpath->query("/html/body//div[@id='outer']/table/tr[not (@class) and (./td)]/td");

    $arrTh = $this->txtNodes_to_array($nodesTh);
    $arrTd = $this->txtNodes_to_array($nodesTd);

    $carArray = array_combine($arrTh, $arrTd);
    return $carArray;
  }
  function set_car($carArray, &$CarsArray,$logTabulation)
  {
    $CarsArray[$carArray["Brand"]][$carArray["Model"]][$carArray["Generation"]][$carArray["Modification (Engine)"]] = $carArray;
    
    $time = new DateTimeImmutable();
    // time +"\n" + formatted arr 
    $txt = $time->format("[H:i:s.u]")."\n".
    print_r($CarsArray[$carArray["Brand"]][$carArray["Model"]][$carArray["Generation"]][$carArray["Modification (Engine)"]]["Brand"],true)." ".
    print_r($CarsArray[$carArray["Brand"]][$carArray["Model"]][$carArray["Generation"]][$carArray["Modification (Engine)"]]["Model"],true)."(".
    print_r($CarsArray[$carArray["Brand"]][$carArray["Model"]][$carArray["Generation"]][$carArray["Modification (Engine)"]]["Generation"],true).") ".
    print_r($CarsArray[$carArray["Brand"]][$carArray["Model"]][$carArray["Generation"]][$carArray["Modification (Engine)"]]["Modification (Engine)"],true)." added";
      
    $logFormattedTxt = str_replace("\n","\n".str_repeat(" ",$logTabulation),$txt);
    file_put_contents(__DIR__ ."/logs.txt",$logFormattedTxt, FILE_APPEND);
  }

}
