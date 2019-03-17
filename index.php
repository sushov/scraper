<?php

  require('phpQuery/phpQuery.php');
  require('WebSpider.php');
  ini_set('memory_limit', '900M');
  ini_set('max_execution_time', 800);
  set_time_limit(0);

  // This is our starting point. Change this to whatever URL you want.
  $url = 'https://propertylink.estatesgazette.com/find-an-agent?a_to_z=a';
  $agents = [];

  if (!isset($_SERVER["argc"]) || !$_SERVER["argc"])
  {
    echo "This file is intended to be run from the command-line.";

    exit();
  }

  function getData($url){
    $spider = new WebSpider(); 
    $spider->fetchPage($url);
    $first_scrap = $spider->html;
    PhpQuery::newDocumentHTML($first_scrap);
    $results = pq(".filter")->find(".letter");
    $re = '/<a class="letter" href="(.*?)">(.*?)<\/a>/m';
    preg_match_all($re, $results, $matches, PREG_SET_ORDER, 0);
    foreach ($matches as $match) {
      $spider->fetchPage('https://propertylink.estatesgazette.com/'.$match[1]);
      $second_scrap = $spider->html;
      PhpQuery::newDocumentHTML($second_scrap);
      $results2 = pq(".agents__result-body")->find(".btn");
      $re2 = '/<a class="btn btn--block btn--outline js-ga-click-event " data-ga-name="Agent details" data-ga-value="(.*?)" href="(.*?)">View Profile<\/a>/ms';
      preg_match_all($re2, $results2, $matches2, PREG_SET_ORDER, 0);
      foreach ($matches2 as $matche2) {
        $spider->fetchPage('https://propertylink.estatesgazette.com'.$matche2[2]);
        $third_scrap = $spider->html;
        PhpQuery::newDocumentHTML($third_scrap);
        $agentName[] = pq("h2")->text();
        $agents['agentDescription'] = pq("#about")->text();
        $agents['office_info'] = preg_replace('/\s+/', '', pq('#offices')->text());
        $agents['numberOfProperties'] = pq(".agents__details-listing-count")->text();
          $agents['servicesProvided'] = pq("#services")->text();
        $agents['estatesGazetteURL'] = "https://propertylink.estatesgazette.com".$matche2[2];
        $agents['website'] = pq(".js-gwa-link-item")->attr("href");
       // merge all above data in an array here
      }
    }
    saveCsv($agents);
  }

  function saveCsv($data)
  {
     // echo "<pre>";print_r($data);die;
    // foreach ($data as $key => $row) {
    //      echo "<pre>";print_r($row);
    // }die;
    // output headers so that the file is downloaded rather than displayed
    header('Content-type: text/csv');
    header('Content-Disposition: attachment; filename="agents.csv"');
     
    // do not cache the file
    header('Pragma: no-cache');
    header('Expires: 0');
     
    // create a file pointer connected to the output stream
    $file = fopen('php://output', 'w');
     
    // send the column headers
    fputcsv($file, array('agentName', 'agentDescription', 'office_info', 'numberOfProperties', 'servicesProvided', 'estatesGazetteURL', 'website'));
     
    // output each row of the data
    foreach ($data as $row)
    {
    fputcsv($file, $data);
    }
     
    exit();
  }

  // Begin the crawling process by crawling the starting link first.
  getData($url);

?>
