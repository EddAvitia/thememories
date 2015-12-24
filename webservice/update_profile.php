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
	$ObjUser = new User;


	//http://stackoverflow.com/questions/15485354/angular-http-post-to-php-and-undefined
	$postdata = file_get_contents("php://input");
	$response = array(); 
		
	if (isset($postdata)) {
		$request = json_decode($postdata);
		$id					= $request->id;
		$first_name			= $request->first_name;
		$last_name 			= $request->last_name;
		$birth				= $request->birth;
		$gender				= $request->gender;
		$who_can			= $request->who_can;
		$current_password	= $request->current_password;
		
		if($request->new_password){
			$new_password		= $request->new_password;
		}else{
			$new_password = '';
		}
		
		
		$mensaje =  $ObjUser->updateprofile($id, $birth, $birth, $current_password, $new_password, $first_name, $last_name, $gender, $who_can );
		
		$response = array(
			'success' => true,
			'message' => $mensaje
		);
		
		
	}else{
		$response = array(
			'success' => false,
			'message' => "Not called properly with username parameter!"
		);
	}
	
	echo json_encode($response);
?>