<?php
	//http://stackoverflow.com/questions/18382740/cors-not-working-php
	if (isset($_SERVER['HTTP_ORIGIN'])) {
		header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
		header('Access-Control-Allow-Credentials: true');
		header('Access-Control-Max-Age: 86400');    // cache for 1 day
	}

	// Access-Control headers are received during OPTIONS requests
	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
			header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
			header("Access-Control-Allow-Headers:        
			{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

		exit(0);
	}
	
	include_once('classes/user.class.php');
	include_once('classes/simpleimage.class.php');
	include_once('classes/memory.class.php');
	$ObjUser = new User;
	$ObjMemory = new Memory;
	
	if (isset($_POST["userId"]) && $_POST["memorydate"]) {
		$userId = $_POST["userId"] ;
		$memorydate = $_POST["memorydate"] ;
		$desc = $_POST["description"] ;
		$fileKey = 'file';
	
		/**
		* You would add more validation, checking image type or user rights.
		**/
		if (!isset($_FILES[ $fileKey ]) || !is_uploaded_file($_FILES[ $fileKey ]['tmp_name'])) 
		{
			$error = 'Invalid Upload';
		}
		
		if (!$error && $_FILES[ $fileKey ]['size'] > 2 * 1024 * 1024)
		{
			$error = 'Please upload only files smaller than 2Mb!';
		}
		 
		if (!$error && !($size = @getimagesize($_FILES[ $fileKey ]['tmp_name']) ) )
		{
			$error = 'Please upload only images, no other files are supported.';
		}
		 
		if (!$error && !in_array($size[2], array(1, 2, 3, 7, 8) ) )
		{
			$error = 'Please upload only images of type JPEG, GIF or PNG.';
		}
		 
		if (!$error && ($size[0] < 25) || ($size[1] < 25))
		{
			$error = 'Please upload an image bigger than 25px.';
		}
		
		if(!$error){
			//TODO everything seems fine with file and params
			$new_image_name =  "app_" .  $ObjMemory->random_string(50) . ".jpg";
			$temp_location = "pics_temp/";
			
			move_uploaded_file($_FILES[ $fileKey ]["tmp_name"], $temp_location.$new_image_name);
			
			$mensaje = $ObjMemory->InsertMemory($memorydate, $new_image_name, $userId, $temp_location, $desc);   
		
			$response = array(
				'success' => true,
				'message' => $mensaje
			);


		
		}else{
			//Error
			$response = array(
				'success' => false,
				'message' => $error,
			);
		}
	}else{
		//Error
		$response = array(
			'success' => false,
			'message' => "Not called properly with username parameter!"
		);
		
	}
	echo json_encode($response);
	
?>