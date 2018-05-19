<?php 

	include "../controllers/responseHandler.php";
	include "../controllers/csv_handler.php";

	$uploads = new Uploads();

	if(isset($_FILES["csv_file"])){
		// call the handler
		$uploads->store_csv();	
	}

	// check for linked_fields
	if(isset($_POST['linked_fields'])){

		// call uploads controller 
		$uploads->load_linked_fields();
	}