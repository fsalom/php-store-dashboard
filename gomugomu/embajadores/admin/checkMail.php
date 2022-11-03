<?php   
       	$email=($_REQUEST['email']);

        $resp = array();
        $email = trim($email);
        if (!$email) {
            $resp = array('ok' => false, 'msg' => '<b style="color:#990000">Debes de escribir un email</b>');
        } else if (!preg_match(
'/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/',
$email)) {
            $resp = array('ok' => false, "msg" => '<b style="color:#990000">Email no válido</b>');
        } else {
            $resp = array("ok" => true, "msg" => '<b style="color:#009900">Email correcto</b>');
        }

	if (@$_REQUEST['do'] == 'check' && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        // means it was requested via Ajax
        echo json_encode($resp);
     exit; // only print out the json version of the response
    }         
?>