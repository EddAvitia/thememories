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
	include_once('classes/memory.class.php');
	$ObjUser = new User;
	$ObjMemory = new Memory;


	//http://stackoverflow.com/questions/15485354/angular-http-post-to-php-and-undefined
	$postdata = file_get_contents("php://input");
	$response = array(); 
		
	if (isset($postdata)) {
		$request = json_decode($postdata);
		$email = $request->email;
		$pswd = $request->pswd;
		
		if($email && $pswd){
			
			if( mysql_num_rows($ObjUser->is_user_activated($email, $pswd) ) > 0 ){
				
				//Random Image
				$base_url = "http://the-memories.com/memoryphoto/";
				$user = mysql_fetch_array( $ObjUser->is_user_activated($email, $pswd) );
				$memory = mysql_fetch_array( $ObjMemory->getRandMemory( $user['id']) );
				
				if($memory){
					$response = array(
						'success' => true,
						'message' => $base_url.$memory['MemoryPhoto'],
						'first_name' => $user['firstname'],
						'last_name' => $user['lastname'],
						'gender' => $user['gender'],
						'birth' => $user['dob'],
						'who_views' => $user['ProfileView'],
						'id' => $user['id']						
					);
					
				}else{
					$response = array(
						'success' => true,
						'message' => null,
						'firstname' => $user['firstname'],
						'lastname' => $user['lastname'],
						'gender' => $user['gender'],
						'birth' => $user['dob'],
						'who_views' => $user['who_views'],
						'who_views' => $user['ProfileView'],
						'id' => $user['id']
					);
				}
				
				
			}else if( mysql_num_rows($ObjUser->obtener_user($email, $pswd) ) > 0 ){
				$response = array(
					'success' => false,
					'message' => "The account ".$email.". has not been activated yet. ".
					"Please check your e-mail inbox for the activation link that was sent when you registered. ".
					"or visit our website to activate your account."
				);
				
			}else{
				$response = array(
					'success' => false,
					'message' => "Email or Password is incorrect"
				);
			}
			
		}else{
			$response = array(
			'success' => false,
			'message' => "Not called properly with username parameter!"
			);
		}
	
	}else {
		$response = array(
			'success' => false,
			'message' => "Not called properly with username parameter!"
		);
	}
	
	echo json_encode($response);
	
?>