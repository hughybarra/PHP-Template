<?php
/* controller stored in controllers/csv_upload.php */
class Uploads{

	/*
	* generateRandomString
	* generate a random file name
	* defaults to 10 characters long 
	*/
	function generateRandomString($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}


	public function store_linked_fields($myArray)
	{
		error_log("THIS SHOULD BE WORKING");

		// validate each and every line 
		// call store link



		// $s = "insert into dbFleshlight.tblOrders set ";


		// foreach($fl_ields as $key => $val){
		// 	$s .= $key."='".$myArray['key'] // foreignSystemOrderID
		// }
		// loop here 
		

	}

	public function load_linked_fields()
	{
		error_log('load linked fields ran');


		error_log(print_r($_POST, true));

		ResponseHandler::json_response('Working', 200);
	}

	/*
	* read_csv_file
	*/
	public function read_csv_file($filePath = '', $fileName = ''){

		// open our csv andparse the first line
		$f = fopen($filePath, 'r');
		$line = fgets($f);
		fclose($f);
		//push the lines into an array
		$lines = explode(",", $line);


		// error_log(print_r($lines, true), 0);

		$user_fields = $lines;
		$csv_file = $fileName;
		$fl_fields = array(
			'intOrder_AffID', 
			'strCust_ShippingCompany',
			'strCust_ShippingFirstName',
			'strCust_ShippingLastName',
			'strCust_ShippingAddress1',
			'strCust_ShippingAddress2',
			'strCust_ShippingCity',
			'strCust_ShippingState',
			'strCust_ShippingZip',
			'strCust_ShippingCountry',
			'strCust_ShippingPhone',
			'foreignSystemOrderID',
		);


		$responseObject = array(
			'user_fields' => $user_fields,
			'fl_fields' => $fl_fields, 
			'csv_file' => $csv_file,
		);

		ResponseHandler::json_response('Success: File Uploaded Successfully', 200, $responseObject);
	}

	/*
	* store_csv
	* @param csvFile, file 
	* attempts to parse over the csv file 
	* opens he file and reads the csv contents
	* parses the data and extracts data into st fields
	* if error returns 400 response
	* if success calls store to databse controller
	*/
	public function store_csv()
	{
		// check for existing files		
		if($_FILES){
			//check for csv fil type 
			if($_FILES['csv_file']['type'] === 'text/csv'){
				// generate our randome file name 
				$randoString = $this->generateRandomString(24);
				// set target directory upload 
				$target_dir = "../uploads/";
				$file_name = $randoString.'.csv';
				// get file path/name
				$target_file = $target_dir . $randoString.'.csv';

				$fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

			    if (move_uploaded_file($_FILES["csv_file"]["tmp_name"], $target_file)) {
			    	error_log('success uploading file', 0);
			    	// ResponseHandler::json_response('Success: File Uploaded Successfully', 200);
			    	$this->read_csv_file($target_file, $file_name);
			        // now that we have saved the file lets process it 
			    } else {
			    	error_log('error uploading file', 0);
			    	ResponseHandler::json_response('Error: Uploading file', 400);
			    }				
			}else{
				error_log('files invalid');
				ResponseHandler::json_response('Error: Invalid file type', 400);
			}



		}else{
			error_log('files do not exist');
			ResponseHandler::json_response('Error: Files missing in upload', 400);
		}			
	}
}