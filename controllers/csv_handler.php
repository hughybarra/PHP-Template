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
	* unlink_file
	* @param file_path String
	* @param target_dir String
	* unlinks a file from the uploads dir 
	*/
	public function unlink_file($file_path = null, $target_dir = null){
		// check for file_path and target_dir
		if($file_path && $target_dir){
			// unlink the file 
			unlink($target_dir.$file_path);
		}

	}// end unlink_file

	/*
	* store_linked_fields
	* @param query_data array 
	* attempts to store the fields in the database 
	*/
	public function store_linked_fields($query_data, $file_name)
	{
		error_log("THIS SHOULD BE WORKING");

		// validate each and every line 
		// call store link



		// $s = "insert into dbFleshlight.tblOrders set ";


		// foreach($fl_ields as $key => $val){
		// 	$s .= $key."='".$myArray['key'] // foreignSystemOrderID
		// }


		// remove the file from the system 
		$this->unlink_file($file_name, '../uploads/');
		// loop here 
		ResponseHandler::json_response('Working', 200);

	}

	/*
	* filter_linked_fields
	* @param $csv_rows array
	* @param $linked_fields array
	* loopsover the $csv_rows and attempts to grab the columns matched by the linked_fields array
	*/
	public function filter_linked_fields($csv_rows = null, $csv_first_row, $linked_fields = null, $file_name = null )
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
			return $query_data;
			// // remove the file from the system 
			// $this->unlink_file($file_name, '../uploads/');
			// // remove dupes 
			// $response_error_array = array_unique($errors_array);
			// ResponseHandler::json_response('Error: CSV fields cannot be empty', 400, $response_error_array);				
		}else{
			error_log('error looping over row stuff');
			return false;
			// $this->store_linked_fields($query_data, $file_name);
		}
	}














	/*
	* load_linked_fields
	* 
	* check for the file path in the linked fields 
	* Check that linked fields exist in POST 
	* 
	* loop over each of the files and open the linked field file 
	* loop through csv rows 
	*/
	public function load_linked_fields()
	{

		/*
		* fl field 
		* user field 
		* file field 
		*/

		// check for files 
		if($_POST['linked_fields']){
			// we have linked fields 
			error_log('we got linked fields');

			// loop over all of our linked fields 
			// determine number of files in our upload 
			$iter = sizeof($_POST['linked_fields']);


			error_log(print_r($_POST['linked_fields'], TRUE));

			$missing_error = false;

			$file_rows = array();

			// check for proper file types 
			for($x = 0; $x < $iter; $x ++){



				error_log('looping');


				error_log(print_r($_POST['linked_fields'][$x]['fl_field'], TRUE));

				$missing_fields = false;

				// check for fl field
				if (isset($_POST['linked_fields'][$x]['fl_field'])) {

				    $fl_field = $_POST['linked_fields'][$x]['fl_field'];
				}else{
					error_log('fl field');
					$missing_fields = true; 
				}

				// check for user field 

				// check for user_field 
				if( isset($_POST['linked_fields'][$x]['user_field'])){
					$user_field = $_POST['linked_fields'][$x]['user_field'];
				}else{
					error_log('user field');
					$missing_fields = true; 
				}

				// check for file field 
				if(isset($_POST['linked_fields'][$x]['file_name'])){
					$file_name = $_POST['linked_fields'][$x]['file_name'];
				}else{
					error_log('file name');
					$missing_fields = true; 
				}



				if($missing_fields){
					error_log('missing fields kick back an error');
					$missing_error = true;
					break; 	
				}

				// make sure we are not missing any data
				if(!$missing_error){
					// set the target dir 
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
					// push rows into our file_rows array 
					array_push($file_rows, $rows);

					$end_data = $this->filter_linked_fields($rows, $first_line,  $_POST['linked_fields'], $file_name);

					error_log(print_r($end_data, true));
					error_log('0-0----');
				}


			}// end for loop 



			// check that we have no missing info 
			if($missing_error){
				error_log('we are missing files');
				// fields missing from post return error
				ResponseHandler::json_response('Error: Missing fields!', 400);
			}else{
				error_log('we are not missing any files');


				// error_log(print_r($file_rows[0], True));
			}

		}else{
			error_log('we dont have linked fields');
		}


		// // check for file path && linked fields 
		// if($_POST['linked_fields'] && $_POST['server_file_path']){
			// $file_name = $_POST['server_file_path'];
			// $target_dir = "../uploads/";
			// $file_path = $target_dir.$file_name;
			// //Open the file.
			// $fileHandle = fopen($file_path, "r");


			// // open our csv andparse the first line
			// $f = fopen($file_path, 'r');
			// $line = fgets($f);
			// fclose($f);
			// //push the lines into an array
			// $first_line = explode(",", $line);


			// $rows = array();
			// //Loop through the CSV rows.
			// while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
			// 	// push all the rows into the array 
			//     array_push($rows, $row);
			// }
		// 	$this->filter_linked_fields($rows, $first_line,  $_POST['linked_fields'], $file_name);

		// }else{
		// 	// remove the file from the system 
		// 	$this->unlink_file($_POST['server_file_path'], '../uploads/');
			// // fields missing from post return error
			// ResponseHandler::json_response('Error: Missing fields', 400);
		// }
	}
















	/*
	* read_csv_files
	* @param $file_array array 
	* loops over the files in the file array 
	* extracts the first line of each file
	* stores the line data, and csv file into an object
	* for each file an object is created
	* creates a required fields array 
	* returns an object with required fields, and an object of lines for each file 
	*/
	public function read_csv_files($file_array = array())
	{
		// check that our files array exists
		if($file_array){
			// get the size of our array 
			$iter = sizeof($file_array);
			// create empty array 
			$file_lines_array = array();

			// loop over all of our files 
			for($x = 0; $x < $iter; $x ++){
				// attempt to open the file at this iter 
				$f = fopen($file_array[$x]['target_file'], 'r');
				$line = fgets($f);
				fclose($f);
				// push lines into an array 
				$lines = explode(',', $line);

				$csv_file = $file_array[$x]['file_name'];

				$temp_array = array(
					'csv_file' => $csv_file, 
					'lines' => $lines, 
				);
				array_push($file_lines_array, $temp_array);
			}// end for loop 


			// finished exploding the lines out of each file 

			// list of required fields for the system 
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
			// create our response object 
			$response_array = array(
				'user_fields' => $file_lines_array, 
				'fl_fields' => $fl_fields,
			);
			ResponseHandler::json_response('Success: File/Files Uploaded Successfully', 200, $response_array);
		}else{
			error_log('missing file array');
			ResponseHandler::json_response('Server Error: Missing File Array', 400);
		}
	}// end read_csv_files

	/*
	* read_csv_file
	*/
	// public function read_csv_file($filePath = '', $fileName = ''){

	// 	// open our csv andparse the first line
	// 	$f = fopen($filePath, 'r');
	// 	$line = fgets($f);
	// 	fclose($f);
	// 	//push the lines into an array
	// 	$lines = explode(",", $line);

	// 	$user_fields = $lines;
	// 	$csv_file = $fileName;
	// 	$fl_fields = array(
	// 		'intOrder_AffID', 
	// 		'strCust_ShippingCompany',
	// 		'strCust_ShippingFirstName',
	// 		// 'strCust_ShippingLastName',
	// 		// 'strCust_ShippingAddress1',
	// 		// 'strCust_ShippingAddress2',
	// 		// 'strCust_ShippingCity',
	// 		// 'strCust_ShippingState',
	// 		// 'strCust_ShippingZip',
	// 		// 'strCust_ShippingCountry',
	// 		// 'strCust_ShippingPhone',
	// 		// 'foreignSystemOrderID',
	// 	);
	// 	$responseObject = array(
	// 		'user_fields' => $user_fields,
	// 		'fl_fields' => $fl_fields, 
	// 		'csv_file' => $csv_file,
	// 	);

	// 	ResponseHandler::json_response('Success: File Uploaded Successfully', 200, $responseObject);
	// }


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

			// determine number of files in our upload 
			$iter = sizeof($_FILES);
			error_log('iter size: '.$iter);

			$file_flag = 0;

			// check for proper file types 
			for($x = 0; $x < $iter; $x ++){
				 // check for correct file types 
				if($_FILES['csv_file_'.$x]['type'] != 'text/csv'){
					error_log('incorrect file type');
					$file_flag = 1;
					break;	
				}
			}// end for loop 


			// check our error flag for valid file types 
			if($file_flag === 1){
				// if error return the error 
				error_log('return an error');
				ResponseHandler::json_response('Error: Uploading file, Only CSV file type allowed', 400);
				exit;
			}

			error_log('all good here');
 		
			// LEFT OFF HERE 
			$file_array = array();
 			// loop over all of the files 
 			for($x = 0; $x < $iter; $x ++){
				// generate our randome file name 
				$randoString = $this->generateRandomString(24);
				// set target directory upload 
				$target_dir = "../uploads/";
				$file_name = $randoString.'.csv';
				// get file path/name
				$target_file = $target_dir . $randoString.'.csv';

				$temp_array = array(
					'target_file' => $target_file, 
					'file_name' => $file_name,
				);

				array_push($file_array, $temp_array);

				if(move_uploaded_file($_FILES['csv_file_'.$x]["tmp_name"], $target_file)) {
					error_log('everything worked');
				}else{
					error_log('something went wrong');
					ResponseHandler::json_response('Error: Uploading file', 400);
				}
 			}// end for loop 

 			error_log('finished looping');
 			error_log(print_r($file_array, TRUE));

 			// now that we have our files uploaded lets call the parser
 			$this->read_csv_files($file_array);


 			// 'csv_file_'+x


			// //check for csv fil type 
			// if($_FILES['csv_file']['type'] === 'text/csv'){
// /

			// 	$fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

			//     if (move_uploaded_file($_FILES["csv_file"]["tmp_name"], $target_file)) {
			//     	$this->read_csv_file($target_file, $file_name);
			//         // now that we have saved the file lets process it 
			//     } else {
			//     	ResponseHandler::json_response('Error: Uploading file', 400);
			//     }				
			// }else{
			// 	ResponseHandler::json_response('Error: Invalid file type', 400);
			// }// end $_FILES['csv_file']['type']
		}else{
			ResponseHandler::json_response('Error: Files missing in upload', 400);
		}// end $_FILES			
	}
}