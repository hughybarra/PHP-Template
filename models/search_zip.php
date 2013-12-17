<?php
	class Search_Zip{
		
		function __construct(){

			// pull in zip from global vars
			if(isset($_POST["zip"])){

				$zip = $_POST["zip"];
			
				// url to yahoo api with zip concated
				$url = "http://query.yahooapis.com/v1/public/yql?q=select%20item%20from%20weather.forecast%20where%20location%3D%22".$zip."%22&format=json"; 

				// loading json from yahoo api. and assigning var
				// Found my answer here. 
				//http://stackoverflow.com/questions/13216636/parsing-complex-json-with-php
				$yahoo_data = file_get_contents($url);
				// json decode the yahoo data for parsing
				$result = json_decode($yahoo_data);

				$success_obj = array (
					"success" => True,
					);
				// parsing down to weekly forcast

				$my_data = $result -> query -> results -> channel -> item -> forecast;
				$location = $result -> query -> results -> channel -> item -> title;
				// echo json_encode($my_data);

				$object = array (
					$my_data, 
					$location,
					);
				
			
				echo json_encode($object);
			}// end isset function
			else {
				$my_data = array (
					"success" => False,
					);
				echo Json_encode($my_data);
			}
		
		}
	}
?>