angular.module('starter.controllers', [])

.controller('LoginCtrl', function($scope, $http, $q, $ionicPopup, $ionicLoading, AuthService, $state ) {
    $scope.data = {};
	$scope.sending = false;
	$scope.forgot_password = false;
	$scope.forgot_password_text = 'Forgot password?';
	$scope.login_btn_text = 'Login';

 
	$scope.preLogin = function() {
		if( $scope.forgot_password ){ recovery(); }else{ login();}
	}
	
	$scope.forgotPassword = function() {
		$scope.forgot_password = !$scope.forgot_password;
		
		if($scope.forgot_password){
			$scope.forgot_password_text = 'Cancel?';
			$scope.login_btn_text = 'Request password';
		}else{
			$scope.forgot_password_text = 'Forgot password?';
			$scope.login_btn_text = 'Login';
		}
		
	};
	
	function login() {
		$scope.sending = true;
		var link = 'http://the-memories.com/includes/app/login.php';
		var postdata = {
			email : $scope.data.email,
			pswd : $scope.data.password
		};
		
		if(postdata.email && postdata.pswd){
			$http.post(link, postdata ).then(function (res){
				$scope.response = res.data;
				
				console.log( JSON.stringify($scope.response) );
				
				
				if($scope.response.success){
					
					AuthService.storePic( $scope.response.message );
					AuthService.storeEmail( $scope.data.email );
					AuthService.storeFirstName( $scope.response.first_name );
					AuthService.storeLastName( $scope.response.last_name );
					AuthService.storeBirth( $scope.response.birth );
					AuthService.storeWhoViews( $scope.response.who_views );
					AuthService.storeGender( $scope.response.gender );
					AuthService.storeID( $scope.response.id );
					
					//Load Options State
					$state.go('options', {}, {reload: true} );
					
				}else{
					var alertPopup = $ionicPopup.alert({
						title: 'Please check your credentials!',
						template: $scope.response.message
					});	
				}
				$scope.sending = false;
			});
			
		}else{
			
			if(!postdata.email){
				var alertPopup = $ionicPopup.alert({
					title: 'Email format is incorrect or is empty!',
					template: ''
				});
				
			}else if(!postdata.pswd){
				var alertPopup = $ionicPopup.alert({
					title: 'Password field cannot be empty!',
					template: ''
				});
			}
			$scope.sending = false;
		}
    };
	
	function facebookLogin(profileInfo) {
		
		var json = JSON.parse(profileInfo);
		console.log( JSON.stringify(profileInfo) );
							
		$scope.sending = true;
		var link = 'http://the-memories.com/includes/app/login_facebook.php';
		var postdata = {
			facebook_id 	: json.id,
			first_name 		: json.first_name,
			last_name 		: json.last_name,
			username 		: json.name,
			birthday 		: json.birthday,
			email 			: json.email,
			gender 			: json.gender[0]
		};
		
		$http.post(link, postdata ).then(function (res){
			$scope.response = res.data;
			
			console.log( JSON.stringify($scope.response) );
			
			if($scope.response.success){
				AuthService.storePic( $scope.response.message );
				AuthService.storeEmail( $scope.response.email );
				AuthService.storeFirstName( $scope.response.first_name );
				AuthService.storeLastName( $scope.response.last_name );
				AuthService.storeBirth( $scope.response.birth );
				AuthService.storeWhoViews( $scope.response.who_views );
				AuthService.storeGender( $scope.response.gender );
				AuthService.storeID( $scope.response.id );
				
				//Load Options State
				$state.go('options', {}, {reload: true} );
				
			}else{
				var alertPopup = $ionicPopup.alert({
					title: 'Please check your credentials!',
					template: $scope.response.message
				});	
			}
			$scope.sending = false;
			$ionicLoading.hide();

		}),function(error){
			console.log(error);
			$ionicLoading.hide();
		};
		
    };
	
	function recovery() {
		$scope.sending = true;
		var link = 'http://the-memories.com/includes/app/forgot.php';
		var postdata = {
			email : $scope.data.email
		};
		
		if(postdata.email){
			$http.post(link, postdata ).then(function (res){
				$scope.response = res.data;
				
				if($scope.response.success){
					
					var alertPopup = $ionicPopup.alert({
						title: "Success",
						template: $scope.response.message
					});
					
				}else{
					var alertPopup = $ionicPopup.alert({
						title: "Failure",
						template: $scope.response.message
					});
					
				}
				$scope.sending = false;

			});
		}else{
			var alertPopup = $ionicPopup.alert({
				title: 'Email format is incorrect or is empty!',
				template: ''
			});
			$scope.sending = false;
		}
		
    }
	
	/************** Facebook Login ****************/
	
	//This method is executed when the user press the "Login with facebook" button
	$scope.facebookSignIn = function() {
		$ionicLoading.show({
			template: 'Logging in...'
		});
		
		facebookConnectPlugin.getLoginStatus(function(success){
			
			console.log('getLoginStatus' + success.status);
			if(success.status === 'connected'){
				// The user is logged in and has authenticated your app, and response.authResponse supplies
				// the user's ID, a valid access token, a signed request, and the time the access token
				// and signed request each expire

				getFacebookProfileInfo(success.authResponse).then(function(profileInfo) {
					// For the purpose of this example I will store user data on local storage
					facebookLogin(profileInfo);
					
				}, function(fail){
					// Fail get profile info
					console.log('profile info fail', fail);
					fbLoginError('profile info fail' + fail );
				});
				
			} else {
				// If (success.status === 'not_authorized') the user is logged in to Facebook,
					// but has not authenticated your app
				// Else the person is not logged into Facebook,
					// so we're not sure if they are logged into this app or not.
				// Ask the permissions you need. You can learn more about
				// FB permissions here: https://developers.facebook.com/docs/facebook-login/permissions/v2.4
				facebookConnectPlugin.login(
					['email', 'public_profile', 'user_birthday' ], fbLoginSuccess, fbLoginError);
			}
		});
	};

	// This is the success callback from the login method
	var fbLoginSuccess = function(response) {
		if (!response.authResponse){
			fbLoginError("Cannot find the authResponse");
			return;
		}

		var authResponse = response.authResponse;
		getFacebookProfileInfo(authResponse).then(function(profileInfo) {
			// For the purpose of this example I will store user data on local storage
			facebookLogin(profileInfo);
			
		}, function(fail){
			// Fail get profile info
			console.log('profile info fail', fail);
			fbLoginError('profile info fail' + fail);
		});
	};

	// This is the fail callback from the login method
	var fbLoginError = function(error){
		var alertPopup = $ionicPopup.alert({
			title: 'Facebook Error :[ )',
			template: JSON.stringify( error )
		});		
		console.log('fbLoginError', error);
		$ionicLoading.hide();
	};

	// This method is to get the user profile info from the facebook api
	var getFacebookProfileInfo = function (authResponse) {
		var info = $q.defer();
		facebookConnectPlugin.api('/me?fields=email,first_name,last_name,birthday,gender,name&access_token=' + authResponse.accessToken, null,
			function (response) {
				console.log( JSON.stringify(response) );
				info.resolve( JSON.stringify(response) );
			},
			function (response) {
				console.log( JSON.stringify( response ) );
				info.reject( JSON.stringify(response) );
			}
		);
		return info.promise;
	};
	
})
	   
.controller('OptionsController', function($scope, $cordovaCamera, AuthService, $state, $ionicPlatform) {
	$scope.data = {};
 	$scope.sending = false;

	$scope.randImage = {
		background: 'url('+AuthService.getRandPic()+')'
	};
	
	$ionicPlatform.registerBackButtonAction(function (event) {
			event.preventDefault();
	}, 100);

	$scope.getPhotoFromFiles = function() {
		$scope.sending = true;
			
		console.log('Getting files');
		var options = {
			quality: 100,
			destinationType: Camera.DestinationType.FILE_URI,
			sourceType: Camera.PictureSourceType.PHOTOLIBRARY,
			mediaType: Camera.MediaType.PICTURE,
			allowEdit: true,
			encodingType: Camera.EncodingType.JPEG,
			popoverOptions: CameraPopoverOptions,
			saveToPhotoAlbum: false,
			correctOrientation:true
		};
			
		$cordovaCamera.getPicture(options).then(function(imageData) {
			console.log(imageData);
			AuthService.storePhoto( imageData );
			
			//Load Image Prepare State
			$scope.sending = false;
			$state.go('image-prepare', {}, {reload: true} );
			
			
		}, function(err) {
			$scope.sending = false;
			console.err(err);
		});
	};
	
	$scope.getPhotoFromCamera = function() {
		$scope.sending = true;
		console.log('Getting camera');
		var options = {
			quality: 100,
			destinationType: Camera.DestinationType.FILE_URI,
			sourceType: Camera.PictureSourceType.CAMERA,
			mediaType: Camera.MediaType.PICTURE,
			allowEdit: true,
			encodingType: Camera.EncodingType.JPEG,
			popoverOptions: CameraPopoverOptions,
			saveToPhotoAlbum: true,
			correctOrientation:true
		};
			
		$cordovaCamera.getPicture(options).then(function(imageData) {
			console.log(imageData);
			AuthService.storePhoto( imageData);
			
			//Load Image Prepare State
			$scope.sending = false;
			$state.go('image-prepare', {}, {reload: true} );
			
		}, function(err) {
			$scope.sending = false;
			console.err(err);
		});
		
	};
	
	$scope.goToProfile = function() {
		//Load Options State
		$state.go('profile', {}, {reload: true} );
	}
  
})

.controller('ImagePrepareController', function($scope, AuthService, $cordovaFileTransfer, $state, $ionicPopup) {
	$scope.data = {};
	$scope.sending = false;
	$scope.photoImage = {
		backgroundImage: 'url('+AuthService.getPhoto()+')'
	};
	
	$scope.cancel = function() {
		//Load Options State
		$state.go('options', {}, {reload: true} );
	}
	
	function getDate(){ 
		var now  = new Date();
		var day = now.getDate();
		var monthIndex = now.getMonth();
		var year = now.getFullYear();
		var fecha = year + '-' + (monthIndex+1) + '-' + day;
		return fecha;
	}
	
	$scope.send = function() {
		$scope.sending = true;
		var link = 'http://the-memories.com/includes/app/upload_image.php';
		var options = {
            fileKey: "file",
			mimeType :"image/jpeg",
			fileName: "image.jpg",
			chunkedMode: false
		};
		
		var params = new Object();
		params.memorydate = getDate();
		params.userId =  AuthService.getID();
		params.description = $scope.data.description;
		options.params = params;
		
		$cordovaFileTransfer.upload(link, AuthService.getPhoto(), options).then(function(result) {
            console.log("SUCCESS: " + JSON.stringify(result.response));
			var alertPopup = $ionicPopup.alert({
				title: "",
				template: JSON.parse(result.response).message
			});
			$scope.sending = false;
			
        }, function(err) {
            console.log("ERROR: " + JSON.stringify(err));
			$scope.sending = false;
			
        }, function (progress) {
            // constant progress updates
        });
		
	};
  
})

.controller('ProfileController', function($scope, $http, $ionicPopup, AuthService, $state) {
	$scope.data = {};
	$scope.sending = false;
	$scope.data.first_name = AuthService.getFirstName();
	$scope.data.last_name = AuthService.getLastName();
	
	console.log( "email" +  AuthService.getEmail());
	
	$scope.data.user_email = AuthService.getEmail();
	formatDate();
	formatGender();
	formatViews();
	
	
	function formatDate(){
		var birth = AuthService.getBirth();
		var now  = new Date();
		if(birth){
			now = new Date( Date.parse( AuthService.getBirth() ) );
		}
		
		$scope.birth = {
			value: now
		};
	}
	
	function formatGender(){
		//M - male - false
		//F - female -true
		if( AuthService.getGender() == "F" ){
			$scope.data.gender = true;
		}else{
			$scope.data.gender = false;
		}
	}
	
	function formatViews(){
		//1 - friends - false
		//0 - everyone - true
		if(AuthService.getWhoViews() == 1){
			$scope.data.who_can_view = false;
		}else{
			$scope.data.who_can_view = true;
		}

	}


 	$scope.cancel = function() {
		//Load Options State
		$state.go('options', {}, {reload: true} );
	}
	
	$scope.update = function() {
		$scope.sending = true;
		var link = 'http://the-memories.com/includes/app/update_profile.php';
		var postdata = {
			id				: AuthService.getID(),
			first_name		: $scope.data.first_name,
			last_name 		: $scope.data.last_name,
			birth			: getDate(),
			gender			: theGender(),
			who_can			: whoCanView(),
			current_password: $scope.data.current_password,
			new_password	: $scope.data.new_password
		};
		
		function theGender(){ return ( $scope.data.gender )? 'F':'M'; }
		function whoCanView(){ return ($scope.data.who_can_view == false)? '1':'2'; }
		function getDate(){ 
				var date = $scope.birth.value;
				var day = date.getDate();
				var monthIndex = date.getMonth();
				var year = date.getFullYear();
				var fecha = year + '-' + (monthIndex+1) + '-' + day;
				return fecha;
		}
		
		if( !(	
			postdata.id && 
			postdata.first_name && 
			postdata.last_name &&
			postdata.birth &&
			postdata.gender &&
			postdata.who_can &&
			postdata.current_password) ){
			
			var alertPopup = $ionicPopup.alert({
				title: "Error",
				template: "All Fields are required."
			});
				
		}else if( 
			($scope.data.new_password  || $scope.data.confirm_password)&&
			$scope.data.new_password != $scope.data.confirm_password){
			
			var alertPopup = $ionicPopup.alert({
				title: "Error",
				template: "New Password don't match."
			});
			
		}else{
			
			
			$http.post(link, postdata ).then(function (res){
				$scope.response = res.data;
				console.log( JSON.stringify($scope.response) );

				var alertPopup = $ionicPopup.alert({
						title: $scope.response.message,
						template: ''
				});
				$scope.sending = false;

			});
			
		}
		$scope.sending = false;
    };
	
});