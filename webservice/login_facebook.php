<?php
	include "../common.php";
	include_once('includes/headers.php');
	include_once('classes/memory.class.php');
	include_once('classes/user.class.php');
	include_once $config['SiteClassPath']."class.user.register.php";
	$ObjUserRegister	= new User_Register();
	$ObjMemory = new Memory;
	$ObjUser = new User;

	//http://stackoverflow.com/questions/15485354/angular-http-post-to-php-and-undefined
	$postdata = file_get_contents("php://input");
	$response = array(); 
		
	if (isset($postdata)) {
		$request 		= json_decode($postdata);
		$facebook_id 	= $request->facebook_id;
		$first_name 	= $request->first_name;
		$last_name 		= $request->last_name;
		$username 		= $request->username;
		$birthday 		= $request->birthday;
		$email 			= $request->email;
		$gender 		= $request->gender;
		
		if($email && $facebook_id ){
			
			// check if user in db => login
			if( mysql_num_rows($ObjUser->check_facebook_user($facebook_id) ) > 0 ){
				
				$user = mysql_fetch_array( $ObjUser->check_facebook_user($facebook_id) );
				//Random Image
				$memory = mysql_fetch_array( $ObjMemory->getRandMemory( $user['id']) );				
				$base_url = "http://the-memories.com/memoryphoto/";
				$message = ($memory)? $base_url.$memory['MemoryPhoto']: null;
				$success = true;
				
				if(	$user['status'] == "0"){
					//User is blocked
					$message = "Your account has been blocked. Please contact site administrator";
					$success = false;
				}
				
				$response = array(
					'success' => $success,
					'message' => $message,
					'first_name' => $user['firstname'],
					'last_name' => $user['lastname'],
					'gender' => $user['gender'],
					'birth' => $user['dob'],
					'who_views' => $user['ProfileView'],
					'email' => $user['email'],
					'id' => $user['id']						
				);
				

			}else{
				//user not in db
				$response = $ObjUser->createUser($first_name, $last_name, $username, $birthday, $email, $facebook_id, $gender);
				if($response && $response['id']){
					
					$ObjUserRegister->SentFacebookPasswordEmail($email, $response['temp_pass'], $first_name, $last_name);

					$response = array(
						'success' => true,
						'message' => 'A new user had been created',
						'first_name' => $first_name,
						'last_name' => $last_name,
						'gender' => $gender,
						'birth' => $birthday,
						'email' => $email,
						'who_views' => 1,
						'id' => $response['id']					
					);
				}else{
					$response = array(
						'success' => false,
						'message' => "Not called properly with user_id parameter! DB->Error"
					);
				}
			}
			
		}else{
			$response = array(
			'success' => false,
			'message' => "Not called properly with facebook_id parameter!"
			);
		}
	
	}else {
		$response = array(
			'success' => false,
			'message' => "Not called properly with facebook_id parameter!"
		);
	}
	
	echo json_encode($response);
	
?>