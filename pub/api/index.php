<?php
require '../../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;

$app = new Application;
#$app['debug'] = true; // enable this to get error description
($app['endpoint']=getenv('LDC_ENDPOINT'))||($app['endpoint']='http://pub.linkeddata.center/demo/sparql');
($app['user']=getenv('LDC_USER'))||($app['user']='demo');
($app['password']=getenv('LDC_PASSWORD'))||($app['password']='demo');


$app->get('/', function( Request $request, Application $app) {
	
	// get paramethers from querystring
	$class 	= filter_var( $request->query->get('class', 'Automobile'),FILTER_SANITIZE_STRING);
	$term 	= filter_var( $request->query->get('term',''),FILTER_SANITIZE_STRING);
	$lang 	= filter_var( $request->query->get('lang', 'en'),FILTER_SANITIZE_STRING);
	$list 	= filter_var( $request->query->get('list', 10),FILTER_SANITIZE_NUMBER_INT);
	
	if(strlen($term)<2) { $app->abort(400,'Search  lengh must be at least 2 char.');}
	if( $list < 1)  { $app->abort(400,'List must be non negative.');}
	
	// Prepare a SPARQL query
	$sparqlQuery = "
		PREFIX dbo: <http://dbpedia.org/ontology/>
		PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
		SELECT ?label FROM <urn:autocomplete:dbpedia> 
		WHERE {
			?s a dbo:$class ;rdfs:label ?label .
			FILTER(LANGMATCHES(LANG(?label), '$lang') && REGEX(?label, '^$term','i'))
		} LIMIT $list
	";
	
	// Call sparql endpoint and get data
	$client = new GuzzleHttp\Client();
	$res = $client->request('POST', $app['endpoint'], array(
    	'auth' => array($app['user'], $app['password']),
    	'headers' => array(
    		'Content-Type' => 'application/sparql-query',
    		'Accept' => 'text/csv',
		),
		'body' => $sparqlQuery
	));
	
	// format results as required by app
	$resultAsArray = explode("\n",trim((string) $res->getBody()));
	array_shift($resultAsArray); // skips headers
	
	return $app->json($resultAsArray);
	
});

$app->run();