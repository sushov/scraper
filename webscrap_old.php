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
        $agentName = pq("h2")->text();
        $agentDescription = pq("#about")->text();
        $office_location = preg_replace('/\s+/', '', pq('#offices')->find('div:first')->find('p:first')->text());
        // $phone= clean_phone();
        // $email= clean_email();
        echo $phone;
        // $zipCode = pq(".agents__details-listing-count")->text();
        $numberOfProperties = pq(".agents__details-listing-count")->text();
        $servicesProvided = pq("#services")->text();
        $estatesGazetteURL = "https://propertylink.estatesgazette.com".$matche2[2];
        $website = pq(".js-gwa-link-item")->attr("href");
        // echo "<pre>";print_r($office_location);
        // echo "<pre>";print_r($third_scrap);
      }
    }die;
  }


  // function clean_phone()
  // {
  //   $first = preg_replace('/\s+/', '', pq('#offices')->find('div:last')->find('p:last')->text());
  //   $second = explode("E:",$first);
  //   $second = explode("T:",$second[0]);
  //   return $second[1];
  // }

  // function clean_email()
  // {
  //   $first = preg_replace('/\s+/', '', pq('#offices')->find('div:last')->find('p:last')->text());
  //   $second = explode("E:",$first);
  //   $second = explode("T:",$second[1]);
  //   return $second[0];
  // }

  //function clean_address();
  // {
  //   $first = preg_replace('/\s+/', '', pq('#offices')->find('div:first')->find('p:first')->text());
  //   $second = $explode("E:",$first);
  //   $second = $explode("T:",$second[1]);
  //   return $second[0];
  // }

  // Begin the crawling process by crawling the starting link first.
  getData($url);

?>
