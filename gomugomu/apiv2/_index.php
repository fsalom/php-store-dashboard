<?php
error_reporting(-1);
ini_set('display_errors', 1);
require '../vendor/autoload.php';
$app = new Slim\App();

$app->get('/books', function() {

 $data = array('<foo>',"'bar'",'"baz"','&blong&');
 echo json_encode($data);
 
});

$app->run();
?>