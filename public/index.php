<?php

	// show php info 
	// var_dump(phpinfo());


	// define('ROOTPATH', __DIR__);

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	error_log("App Running", 0);

	// include
	// =============================
	include "../models/view_model.php";
	include "../controllers/upload_csv.php";
	include "../controllers/responseHandler.php";
	// end includes

	// declares 
	// =============================
	$uploads = new Uploads();
	$view_model = new View_Model();

	// error_log("Get Ran", 0);
	// check for action in url
	// ===========================
	if (empty($_GET["action"])){
		// error_log($_GET['action'], 0 );
		$action = "home";
	}else{
		error_log($_GET['action'], 0 );
		$action = $_GET["action"];
	}
	// ===========================

	// error_log($action, 0);

	if( $action === "home"){
		$view_model->get_view("../views/index.html");
	}