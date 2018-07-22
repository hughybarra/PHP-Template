<?php 

	include "../controllers/responseHandler.php";
	include "../controllers/csv_handler.php";

	$uploads = new Uploads();

	// call the handler
	$uploads->store_csv();	