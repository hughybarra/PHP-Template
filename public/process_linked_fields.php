<?php 

	include "../controllers/responseHandler.php";
	include "../controllers/csv_handler.php";

	$uploads = new Uploads();



	// call uploads controller 
	$uploads->load_linked_fields();
