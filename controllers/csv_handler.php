<?php
/* controller stored in controllers/csv_upload.php */
class Uploads{

	/* 
	* A wild Megalodon Holy shit
		                                 ,-
		                               ,'::}
		                              /::::}
		                            ,'::::o\                                      _..
		         ____........-------,..::?88b                                  ,-' /
		 _.--"""". . . .      .   .  .  .  ""`-._                           ,-' .;'
		<. - :::::o......  ...   . . .. . .  .  .""--._                  ,-'. .;'
		 `-._  ` `":`:`:`::}}}}:::::::::::::::::.:. .  ""--._ ,' }    ,-'.  .;'
		     """_=--       //'doo.. ````:`:`::::::::::.:.:.:. .`-`._-'.   .;'
		         ""--.__     P(       \               ` ``:`:``:::: .   .;'
		                "\""--.:-.     `.                             .:/
		                  \. /    `-._   `.""-----.,-..::(--"".\""`.  `:\
		                   `P         `-._ \          `-:\          `. `:\
		                                   ""            "            `-._) 
	*/

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


	/*
	* store_linked_fields
	* @param query_data array 
	* attempts to store the fields in the database 
	*/
	public function store_linked_fields($query_data)
	{
		error_log("THIS SHOULD BE WORKING");

		// validate each and every line 
		// call store link



		// $s = "insert into dbFleshlight.tblOrders set ";


		// foreach($fl_ields as $key => $val){
		// 	$s .= $key."='".$myArray['key'] // foreignSystemOrderID
		// }
		// loop here 
		ResponseHandler::json_response('Working', 200);

	}

	/*
	* filter_linked_fields
	* @param $csv_rows array
	* @param $linked_fields array
	* loopsover the $csv_rows and attempts to grab the columns matched by the linked_fields array
	*/
	public function filter_linked_fields($csv_rows = null, $csv_first_row, $linked_fields = null )
	{
		// remove first index from csv rows 
		unset($csv_rows[0]);

		$query_data = array();
		$errors_array = array();
		$error_flag = false;

		// lopo over linked fields and start processing the csv
		foreach ($linked_fields as &$link) 
		{
			// csv_first_row counter
			$csv_first_row_index = 0; 

			// loop overeach $csv_first_row and find a match to our linked_fields
			foreach($csv_first_row as &$row)
			{
				$csv_first_row_index ++;
				// check if we have a matching row and link name 
				if($row === $link['user_field']){
					$query_data[$link['user_field']] = array();
					break;
				}//end row user_field chck 
			}// end $csv_first_row as row

			// now that we have our index lets grab the data from our csv and stor it into some sort of sexy value 
			// $csv_first_row_index
			for ($i = 1; $i <= sizeof($csv_rows); $i++) {
				// check that our row has data
				if($csv_rows[$i][$csv_first_row_index -1] === '' ){
					// row is missing data 
					$error_flag = true;
					// push row nameinto error array 
					array_push($errors_array, $link['user_field']);
				}
				// push field into query data array 
				array_push($query_data[$link['user_field']], $csv_rows[$i][$csv_first_row_index -1]);
			}// end $csv_rows_length loop 
		}// nd $linked_fields as link 
		unset($link);
		unset($row);
		// check if we have any errors
		if($error_flag){
			// remove dupes 
			$response_error_array = array_unique($errors_array);
			ResponseHandler::json_response('Error: CSV fields cannot be empty', 400, $response_error_array);				
		}else{
			$this->store_linked_fields($query_data);
		}
	}

	public function load_linked_fields()
	{


		// check for file path && linked fields 
		if($_POST['linked_fields'] && $_POST['server_file_path']){
			$file_name = $_POST['server_file_path'];
			$target_dir = "../uploads/";
			$file_path = $target_dir.$file_name;
			//Open the file.
			$fileHandle = fopen($file_path, "r");


			// open our csv andparse the first line
			$f = fopen($file_path, 'r');
			$line = fgets($f);
			fclose($f);
			//push the lines into an array
			$first_line = explode(",", $line);


			$rows = array();
			//Loop through the CSV rows.
			while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
				// push all the rows into the array 
			    array_push($rows, $row);
			}
			$this->filter_linked_fields($rows, $first_line,  $_POST['linked_fields']);

		}else{
			// fields missing from post return error
			ResponseHandler::json_response('Error: Missing fields', 400);
		}
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

		$user_fields = $lines;
		$csv_file = $fileName;
		$fl_fields = array(
			'intOrder_AffID', 
			'strCust_ShippingCompany',
			'strCust_ShippingFirstName',
			// 'strCust_ShippingLastName',
			// 'strCust_ShippingAddress1',
			// 'strCust_ShippingAddress2',
			// 'strCust_ShippingCity',
			// 'strCust_ShippingState',
			// 'strCust_ShippingZip',
			// 'strCust_ShippingCountry',
			// 'strCust_ShippingPhone',
			// 'foreignSystemOrderID',
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
			    	$this->read_csv_file($target_file, $file_name);
			        // now that we have saved the file lets process it 
			    } else {
			    	ResponseHandler::json_response('Error: Uploading file', 400);
			    }				
			}else{
				ResponseHandler::json_response('Error: Invalid file type', 400);
			}// end $_FILES['csv_file']['type']
		}else{
			ResponseHandler::json_response('Error: Files missing in upload', 400);
		}// end $_FILES			
	}
}