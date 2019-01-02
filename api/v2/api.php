<?php 

$apiCallVariables = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

if(count($apiCallVariables) > 6)
{
    header($_SERVER['SERVER_PROTOCOL'] . " 400 Bad Request");
    die();
}

$controllerName = isset($apiCallVariables['3']) ? $apiCallVariables['3'] : "";
$functionCall = isset($apiCallVariables['4']) ? $apiCallVariables['4'] : "";
$functionVal = isset($apiCallVariables['5']) ? $apiCallVariables['5'] : "";

if(!file_exists('controllers/'.$controllerName.'.php'))
{
    header($_SERVER['SERVER_PROTOCOL']." 404 Not found.");
    die();
}

require('controllers/'.$controllerName.'.php');
$controller = new $controllerName();
$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) 
{
    case 'GET':
    case 'POST':
    case 'DELETE':
    {
        if($functionVal !== "") 
        {
            $controller->$functionCall($functionVal);
        }
        else 
        {
            $controller->$functionCall();
        }  
        break;
    }
    default:
    {
        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not allowed');
        break;
    }
}
?>