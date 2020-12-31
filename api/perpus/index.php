<?php
/**
 * Simple example of web service
 * @author R. Bartolome
 * @version v1.0
 * @return JSON messages with the format:
 * {
 * 	"code": mandatory, string '0' for correct, '1' for error
 * 	"message": empty or string message
 * 	"data": empty or JSON data
 * }
 *
 * This file can be tested from the browser:
 * http://localhost/webservice-php-json/service_test.php
 *
 * Based on
 * http://www.raywenderlich.com/2941/how-to-write-a-simple-phpmysql-web-service-for-an-ios-app
 */

// the API file
require_once 'api.php';

// creates a new instance of the api class
$api = new api();

// message to return
$message = array();

switch($_POST["api"])
{
	case 'data':
		if ($_POST["id"] == "") {
			$message["code"] = "1";
			$message["message"] = "Id cannot null";
			break;
		}
		$params = array();
		$params['id'] = isset($_POST["id"]) ? $_POST["id"] : '';
		if (is_array($data = $api->data($params))) {
			$message["code"] = "0";
			$message["data"] = $data;
		} else {
			$message["code"] = "1";
			$message["message"] = "Error on get method";
		}
		break;

	case 'history':
		if ($_POST["id"] == "") {
			$message["code"] = "1";
			$message["message"] = "Id cannot null";
			break;
		}
		$params = array();
		$params['id'] = isset($_POST["id"]) ? $_POST["id"] : '';
		if (is_array($data = $api->history($params))) {
			$message["code"] = "0";
			$message["data"] = $data;
		} else {
			$message["code"] = "1";
			$message["message"] = "Error on get method";
		}
		break;

	case 'filter':
		$params = array();
		$params['search'] = isset($_POST["search"]) ? $_POST["search"] : '';
		if (is_array($data = $api->filter($params))) {
			$message["code"] = "0";
			$message["data"] = $data;
		} else {
			$message["code"] = "1";
			$message["message"] = "Error on get method";
		}
		break;
	
	case 'login':
		$params = array();
		$params['id'] = isset($_POST["id"]) ? $_POST["id"] : '';
		$params['password'] = isset($_POST["password"]) ? $_POST["password"] : '';
		if ($params['id'] == "") {
			$message["code"] = "1";
			$message["message"] = "Id cannot null";
			break;
		}
		if ($params['password'] == "") {
			$message["code"] = "1";
			$message["message"] = "Password cannot null";
			break;
		}
		$data = $api->login($params);
		if ($data != null && !is_string($data)) {
			$message["code"] = "0";
			$message["message"] = "Successfully Login";
			$message["data"] = $data;
		} else if (is_string($data) && $data != null) {
			$message["code"] = "1";
			$message["message"] = $data;
		} else {
			$message["code"] = "1";
			$message["message"] = "Error on get method";
		}
		break;

	default:
		$message["code"] = "1";
		$message["message"] = "Unknown api " . $_POST["api"];
		break;
}

//the JSON message
header('Content-type: application/json; charset=utf-8');
echo json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

?>
