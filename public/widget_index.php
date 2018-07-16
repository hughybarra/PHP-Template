<?php 

	// include
	// =============================
	include "../models/view_model.php";
	include "../controllers/upload_csv.php";
	include "../controllers/responseHandler.php";


	// declares 
	// =============================
	$uploads = new Uploads();
	$view_model = new View_Model();
	// return the view model 
	$view_model->get_view("../views/index.html");