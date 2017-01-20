<?php
$a=0;
include_once __DIR__ . '/include/DBHandler.php';
$method = substr($_SERVER['REQUEST_URI'], strlen(substr($_SERVER['SCRIPT_NAME'],0, strlen($_SERVER['SCRIPT_NAME']) - 9)));
$db = new DbHandler();
$GLOBALS['db'] = $db;
$result = "";
$param=NULL;
$input=explode("/",$method);
if($input[0]=="api" && $input[1]=="task") {


    $num = count($input);
    if ($num == 2) $param = "-1";
    if ($num==3) $param=$input[2];



    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $result = $db->getTaskController()->create_task();
    }


    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
       $result = $db->getTaskController()->get_task($param);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        $result = $db->getTaskController()->delete_task($param);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {

        $result=$db->getTaskController()->update_task($param);


    }
    echo $result;
}


else {var_dump(http_response_code(500)); return "{\"status\": 500 }";}


