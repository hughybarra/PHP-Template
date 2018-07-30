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
	* @param query_data object array 
	* Left off right here  
	*/
	public function store_linked_fields($query_data = NULL)
	{

		error_log(print_r($query_data, TRUE));

		/*
		* @DAVID 
		* at this point you now have access to the final query data array 
		* the array is formatted as such 
		* 
		* $product = array(
		* 	'user_field' => someStringNameTheUserProvidedForHisFile, 
		*	'row' => timmy,
		*	'fl_field' => strCust_ShippingFirstName, 
		* );
		* 
		* at this point you would loop over this object and extract the FL_FIELD and the ROW 
		* and then store them in the database. 
		*/


		// return working response 
		ResponseHandler::json_response('Working', 200);
	}

	/*
	* process_filtered_fields
	*/
	public function process_filtered_fields($process_array = null)
	{

		$query_data = array();
		$errors_array = array();
		$error_flag = false;

		// loop over our processed data 
		foreach($process_array as $process_array_item){
			// unset the first index from csv rows 
			unset($process_array_item['rows'][0]);

			// loop over linked fields and start processing the csv
			foreach ($process_array_item['linked_fields'] as &$link) 
			{
				// csv_first_row counter
				$csv_first_row_index = 0; 

				// loop overeach $csv_first_row and find a match to our linked_fields
				// break the array when we have a match to get the index 
				foreach($process_array_item['first_line'] as &$row)
				{
					// increment the counter 
					$csv_first_row_index ++;
					// check if we have a matching row and link name break the loop 
					if($row === $link['user_field']){
						break;
					}//end row user_field chck 
				}// end $csv_first_row as row

				// now that we have our index lets grab the data from our csv and stor it into some sort of sexy value 
				for ($i = 1; $i <= sizeof($process_array_item['rows']); $i++) {
					// check that our row has data
					if($process_array_item['rows'][$i][$csv_first_row_index -1] === '' ){
						// row is missing data 
						$error_flag = true;
						// push row nameinto error array 
						array_push($errors_array, $link['user_field']);
					}else{
						$product = array(
							'user_field' => $link['user_field'], 
							'row' => $process_array_item['rows'][$i][$csv_first_row_index -1], 
							'fl_field' => $link['fl_field'], 
						);
						// push the finished product into the query array 
						array_push($query_data, $product);

						// push field into query data array 
						// array_push($query_data[$link['user_field']], $process_array_item['rows'][$i][$csv_first_row_index -1]);						
					}

				}// end $csv_rows_length loop 
			}// nd $linked_fields as link 
			unset($link);
			unset($row);
		}// end process array loop 


		// check for errors 
		if($error_flag){
			// // remove dupes 
			$response_error_array = array_values(array_unique($errors_array));
			ResponseHandler::json_response('Empty values found in csv', 400, $response_error_array);

		}else{
			// now that we have all our data we can store our linked fields
			$this->store_linked_fields($query_data);
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
		// check for files 
		if($_POST['linked_fields']){
			// loop over all of our linked fields 
			$iter = sizeof($_POST['linked_fields']);

			$missing_error = false;

			$file_rows = array();

			$process_array = array();

			// check for proper file types 
			for($x = 0; $x < $iter; $x ++){

				$missing_fields = false;

				// check for fl field
				if (isset($_POST['linked_fields'][$x]['fl_field'])) {
				    $fl_field = $_POST['linked_fields'][$x]['fl_field'];
				}else{
					$missing_fields = true; 
				}

				// check for user_field 
				if( isset($_POST['linked_fields'][$x]['user_field'])){
					$user_field = $_POST['linked_fields'][$x]['user_field'];
				}else{
					$missing_fields = true; 
				}

				// check for file field 
				if(isset($_POST['linked_fields'][$x]['file_name'])){
					$file_name = $_POST['linked_fields'][$x]['file_name'];
				}else{
					$missing_fields = true; 
				}

				if($missing_fields){
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

					// create new process object to send to the next function
					$process_obj = array(
						'rows' => $rows, 
						'first_line' => $first_line, 
						'linked_fields' => $_POST['linked_fields'], 
						'file_name' => $file_name,
					);

					// pussh obj into process array 
					array_push($process_array, $process_obj);
				}
			}// end for loop 

			// check that we have no missing info 
			if($missing_error){
				// fields missing from post return error
				ResponseHandler::json_response('Error: Missing fields!', 400);
			}else{
				// call our process filtered fields method 
				$this->process_filtered_fields($process_array);
			}

		}else{
			ResponseHandler::json_response('Error: Missing linked fields', 400);
		}
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
			// add to this list, or uncomment to increase the FL fields in the widget 
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
			ResponseHandler::json_response('Server Error: Missing File Array', 400);
		}
	}// end read_csv_files

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

			$file_flag = 0;

			// check for proper file types 
			for($x = 0; $x < $iter; $x ++){
				 // check for correct file types 
				if($_FILES['csv_file_'.$x]['type'] != 'text/csv'){
					$file_flag = 1;
					break;	
				}
			}// end for loop 

			// check our error flag for valid file types 
			if($file_flag === 1){
				// if error return the error 
				ResponseHandler::json_response('Error: Uploading file, Only CSV file type allowed', 400);
				exit;
			}

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

				if(!move_uploaded_file($_FILES['csv_file_'.$x]["tmp_name"], $target_file)) {
					ResponseHandler::json_response('Error: Uploading file', 400);
				}
 			}// end for loop 

 			// now that we have our files uploaded lets call the parser
 			$this->read_csv_files($file_array);
		}else{
			ResponseHandler::json_response('Error: Files missing in upload', 400);
		}// end $_FILES			
	}
}