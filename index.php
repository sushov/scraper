<?php

  require('phpQuery/phpQuery.php');
  require('WebSpider.php');
  ini_set('memory_limit', '900M');
  ini_set('max_execution_time', 800);
  set_time_limit(0);

  // This is our starting point. Change this to whatever URL you want.
  $url = 'https://propertylink.estatesgazette.com/find-an-agent?a_to_z=a';

  // if (!isset($_SERVER["argc"]) || !$_SERVER["argc"])
  // {
  //   echo "This file is intended to be run from the command-line.";

  //   exit();
  // }

  function getData($url){
    $spider = new WebSpider(); 
    $spider->fetchPage($url);
    $first_scrap = $spider->html;
    PhpQuery::newDocumentHTML($first_scrap);
    $results = pq(".filter")->find(".letter");
    $re = '/<a class="letter" href="(.*?)">(.*?)<\/a>/m';
    preg_match_all($re, $results, $matches, PREG_SET_ORDER, 0);
    // foreach ($matches as $match) {
    //   $spider->fetchPage('https://propertylink.estatesgazette.com/'.$match[1]);
    //   $second_scrap = $spider->html;
    //   PhpQuery::newDocumentHTML($second_scrap);
    //   $results2 = pq(".agents__result-body")->find(".btn");
    //   $re2 = '/<a class="btn btn--block btn--outline js-ga-click-event " data-ga-name="Agent details" data-ga-value="(.*?)" href="(.*?)">View Profile<\/a>/ms';
    //   preg_match_all($re2, $results2, $matches2, PREG_SET_ORDER, 0);
    //   foreach ($matches2 as $matche2) {
    //     $spider->fetchPage('https://propertylink.estatesgazette.com'.$matche2[2]);
    //     $third_scrap = $spider->html;
    //     PhpQuery::newDocumentHTML($third_scrap);
    //     $agentName = pq("h2")->text();
    //     $agentDescription = pq("#about")->text();
    //     $office_location = preg_replace('/\s+/', '', pq('#offices')->find('div:first')->find('p:first')->text());
    //     $numberOfProperties = pq(".agents__details-listing-count")->text();
    //     $servicesProvided = pq("#services")->text();
    //     $estatesGazetteURL = "https://propertylink.estatesgazette.com".$matche2[2];
    //     $website = pq(".js-gwa-link-item")->attr("href");
    //     //merge all above data in an array here
    //   }
    // }
    //save the collected data in a csv file here
    saveCsv();
  }

  function saveCsv()
  {
    // output headers so that the file is downloaded rather than displayed
    header('Content-type: text/csv');
    header('Content-Disposition: attachment; filename="agents.csv"');
     
    // do not cache the file
    header('Pragma: no-cache');
    header('Expires: 0');
     
    // create a file pointer connected to the output stream
    $file = fopen('php://output', 'w');
     
    // send the column headers
    fputcsv($file, array('Column 1', 'Column 2', 'Column 3', 'Column 4', 'Column 5'));
     
    $data = array(
    array('Data 11', 'Data 12', 'Data 13', 'Data 14', 'Data 15'),
    array('Data 21', 'Data 22', 'Data 23', 'Data 24', 'Data 25'),
    array('Data 31', 'Data 32', 'Data 33', 'Data 34', 'Data 35'),
    array('Data 41', 'Data 42', 'Data 43', 'Data 44', 'Data 45'),
    array('Data 51', 'Data 52', 'Data 53', 'Data 54', 'Data 55')
    );
     
    // output each row of the data
    foreach ($data as $row)
    {
    fputcsv($file, $row);
    }
     
    exit();
  }

  // Begin the crawling process by crawling the starting link first.
  getData($url);

?>
