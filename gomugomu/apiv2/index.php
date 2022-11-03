<?php
error_reporting(-1);
ini_set('display_errors', 1);
require '../vendor/autoload.php';
require 'include/config.php';
include("include/class.FUNCIONES.php");
include("include/class.APOYO.php");
include("include/class.BECOSOFT.php");
include("include/class.GOMUGOMU.php");
$GOMUGOMU  = new GOMUGOMU();
$FUNCIONES = new FUNCIONES();

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new Slim\App();

$app->get('/tickets/{day}', function(Request $request, Response $response, $args)  {
    $BECOSOFT  = new BECOSOFT();
    $tickets = $BECOSOFT->getTicketsDay($args['day']);
    $response->getBody()->write(json_encode($tickets));
    return $response;
});

$app->get('/searchEAN/{ean}', function (Request $request, Response $response, $args) {
	$BECOSOFT  = new BECOSOFT();
    $response->withStatus(200);
    $items = $BECOSOFT->searchEAN($args['ean']);
    $response->getBody()->write(json_encode($items));
    return $response;
});

$app->run();
?>