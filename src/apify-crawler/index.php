<?php
define('APIFY_CRAWLER_VER', '1.0.0');
define('APIFY_CRAWLER_INCL', __DIR__ . '/../vendor/');
require 'includes/class-apify-crawler.php';
use ApifyCrawler as Crawler;

// Set parameters
$params = array(
       'api' => array(
               'base_uri'  => 'https://api.apify.com',
               'token'     => '7mjr7uTbzLtSbCqXJpnh7iunA',
               'crawler_id'=> 'uwWfDpYbFhP36E8Jv',
           )
       );
// Init Crawler Instance
$crawler    = new Crawler( $params );
$results_id = $crawler->get_id();
$results    = $crawler->get_results( $results_id );

//echo $results_id;
var_dump($results);


