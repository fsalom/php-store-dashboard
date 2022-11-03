<?php

require '../vendor/autoload.php';
require 'include/config.php';
include("include/class.FUNCIONES.php");
include("include/class.APOYO.php");
include("include/class.BECOSOFT.php");
include("include/class.GOMUGOMU.php");

$GOMUGOMU  = new GOMUGOMU();

$FUNCIONES = new FUNCIONES();

// Prepare app
$app = new \Slim\Slim(array(
    'templates.path' => '../templates',
));
$app->response->headers->set('Content-Type', 'application/json');

// Create monolog logger and store logger in container as singleton 
// (Singleton resources retrieve the same log resource definition each time)
$app->container->singleton('log', function () {
    $log = new \Monolog\Logger('slim-skeleton');
    $log->pushHandler(new \Monolog\Handler\StreamHandler('../logs/app.log', \Monolog\Logger::DEBUG));
    return $log;
});

// Prepare view
$app->view(new \Slim\Views\Twig());
$app->view->parserOptions = array(
    'charset' => 'utf-8',
    'cache' => realpath('../templates/cache'),
    'auto_reload' => true,
    'strict_variables' => false,
    'autoescape' => true
);
$app->view->parserExtensions = array(new \Slim\Views\TwigExtension());

// Define routes
$app->get('/', function () use ($app) {
    // Sample log message
    $app->log->info("Slim-Skeleton '/' route");
    // Render index view
    $app->render('index.html');
});

$app->get('/hi/{name}', function ($name) use ($app) {
    // Sample log message
    print($name);
});


$app->get('/hello/:name', function ($name) {
    echo "Hello, " . $name;
});

$app->get('/searchEAN/:ean', function ($ean) {
    $BECOSOFT  = new BECOSOFT();
    $items = $BECOSOFT->searchEAN($ean);
    echo json_encode($items);
});

$app->get('/tickets/:day', function($day) {
    $BECOSOFT  = new BECOSOFT();
    $tickets = $BECOSOFT->getTicketsDay($day);
    echo json_encode($tickets);
});

// Run app
$app->run();



