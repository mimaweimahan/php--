<?php

header('Access-Control-Allow-Origin: *');
header('charset: utf-8');
header('Accept: application/json');
header('Content-type: application/json');


$con =file_get_contents("php://input");
file_put_contents('/tmp/callbackrecharge'.time().'.log',$con);

$rsp = ['error'=>'000','errmsg'=>'Success'];
echo json_encode($rsp);