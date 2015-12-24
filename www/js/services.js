angular.module('starter.services', [])

.service('AuthService', function($q, $http, USER_DATA) {
	
	var DEFAULT_PIC = 'img/thumb.png';
	var DEFAULT_PHOTO = 'img/thumb.png';
  
	var storeEmail = function(email) {
		window.localStorage.setItem(USER_DATA.email, email);
	}
	var storePic = function(pic) {
		if(!pic){ pic = DEFAULT_PIC; }
		window.localStorage.setItem(USER_DATA.pic, pic);
	}
	
	var storePhoto = function(photo) {
		if(!photo){ photo = DEFAULT_PHOTO; }
		window.localStorage.setItem(USER_DATA.photo, photo);
	}
	
	var storeFirstName = function(name) {
		window.localStorage.setItem(USER_DATA.first_name, name);
	}
	
	var storeLastName = function(name) {
		window.localStorage.setItem(USER_DATA.last_name, name);
	}
	
	var storeBirth = function(birth) {
		window.localStorage.setItem(USER_DATA.birth, birth);
	}
	
	var storeWhoViews = function(who) {
		window.localStorage.setItem(USER_DATA.who_views, who);
	}
	
	var storeGender = function(gender) {
		window.localStorage.setItem(USER_DATA.gender, gender);
	}
	
	var storeID = function(id) {
		window.localStorage.setItem(USER_DATA.id, id);
	}
				
	var getRandPic = function() {
		
		console.log( window.localStorage.getItem(USER_DATA.pic) );
		
		return window.localStorage.getItem(USER_DATA.pic);
	}
	
	var getEmail = function() {
		return window.localStorage.getItem(USER_DATA.email);
	}
	
	var getPhoto = function() {
		return window.localStorage.getItem(USER_DATA.photo);
	}
	
	var getFirstName = function() {
		return window.localStorage.getItem(USER_DATA.first_name);
	}
	
	var getLastName = function() {
		return window.localStorage.getItem(USER_DATA.last_name);
	}
	
	var getBirth = function() {
		return window.localStorage.getItem(USER_DATA.birth);
	}
	
	var getWhoViews = function() {
		return window.localStorage.getItem(USER_DATA.who_views);
	}	
	
	var getGender = function() {
		return window.localStorage.getItem(USER_DATA.gender);
	}
	
	var getID = function() {
		return window.localStorage.getItem(USER_DATA.id);
	}
	
	return {
		storeEmail		: storeEmail,
		storePic		: storePic,
		storePhoto		: storePhoto,
		storeFirstName	: storeFirstName,
		storeLastName	: storeLastName,
		storeBirth		: storeBirth,
		storeWhoViews	: storeWhoViews,
		storeGender		: storeGender,
		storeID			: storeID,
		getRandPic		: getRandPic,
		getEmail		: getEmail,
		getPhoto		: getPhoto,
		getFirstName	: getFirstName,
		getLastName		: getLastName,
		getBirth		: getBirth,
		getWhoViews		: getWhoViews,
		getGender		: getGender,
		getID			: getID
	};
});