<?php
require '../../vendor/autoload.php';

use BOTK\Context\Context;
use BOTK\Context\ContextNameSpace as V;

class AutocompleteController extends \BOTK\Core\EndPoint {
	// Here LinkedData.Center credentials
	const ENDPOINT = 'http://pub.linkeddata.center/';
	const KID = 'demo';
	const SECRET = 'demo';

	protected function setRoutes() {
		$this -> get('/', function() {

			// fetch and validate inputs
			$ns = Context::factory() -> ns(INPUT_GET);
			$class = $ns -> getValue('class', 'Automobile', V::ENUM('Automobile|River|Mammal'));
			$term = $ns -> getValue('term', V::MANDATORY, V::STRING('/.{2,}/'), FILTER_SANITIZE_STRING);
			$lang = $ns -> getValue('lang', 'en', null, FILTER_SANITIZE_STRING);
			$list = $ns -> getValue('list', 10, V::POSITIVE_INT(), FILTER_SANITIZE_NUMBER_INT);

			\BOTK\RDF\HttpClient::useIdentity(self::KID, self::SECRET);
			$sparql = new EasyRdf_Sparql_Client(self::ENDPOINT . self::KID . '/sparql');

			// define sparql query
			$query = "
				PREFIX dbo: <http://dbpedia.org/ontology/>
				PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
				SELECT ?label FROM <urn:autocomplete:dbpedia> 
				WHERE {
					?s a dbo:$class ;rdfs:label ?label .
					FILTER(LANGMATCHES(LANG(?label), '$lang') && REGEX(?label, '^$term','i'))
				} LIMIT $list
			";
			$solutions = $sparql -> query($query);

			// create a simple json array from retrived solutions
			$result = array();
			foreach ($solutions as $row) {
				$result[] = $row -> label -> getValue();
			}

			// return the result as a Json representation
			return json_encode($result, JSON_PRETTY_PRINT);
		});
	}

}

try {
	echo \BOTK\Core\EndPointFactory::make('AutocompleteController') -> run();
} catch ( Exception $e) {
	echo \BOTK\Core\ErrorManager::getInstance() -> render($e);
}
