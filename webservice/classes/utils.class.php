<?php
class Utils{

 	//Constructor
	function Utils(){

	}
	
	function gen_md5_password($len = 8) {
		// function calculates 32-digit hexadecimal md5 hash
		// of some random data
		return substr(md5(rand().rand()), 0, $len);
	}
}
?>