<?php
 include_once('conexion.class.php');
 
 class Memory{
 	var $con;

 	//Constructor
	function Memory(){
		$this->con=new DBManager;
	}
	
	function getRandMemory($memberId){
		if($this->con->Conectar()== true){
			$sql="select *,DATE_FORMAT(MemoryDate,'%b %d,%Y') as MemoryDate from tbl_mymemories where MemberID='".$memberId."' ORDER BY rand() LIMIT 1";
			return mysql_query($sql);			
		}
	}
	
	
	function random_string($length) {
		$key = '';
		$keys = array_merge(range(0, 9), range('a', 'z'));
		
		for ($i = 0; $i < $length; $i++) {
			$key .= $keys[array_rand($keys)];
		}
		return $key;
	}


	function changeDateformut($date) 
	{
		if($date!='' && $date!='0000-00-00')
		{
			$formuttedDate=explode("-",$date);
			$formuttedDateRet=$formuttedDate[2]."-".$formuttedDate[0]."-".$formuttedDate[1];
			return $formuttedDateRet;
		}
	}
	
	function InsertMemory($memorydate, $imgname, $userid, $location, $title ) {   
		global $objSmarty,$config;
		
		if($this->con->Conectar()== true){
		
			$memdate = $this->changeDateformut( $memorydate );
			$memoryphoto = "";
			$root_url = $_SERVER[DOCUMENT_ROOT].'/memoryphoto/';
			$location_url = $_SERVER[DOCUMENT_ROOT].'/includes/app/pics_temp/';
			
			if($imgname  != "")
			{
				$resizeObj = new SimpleImage( $location.$imgname );
				
				if($width>="1000"){
					$resizeObj -> resize(800,650);
					$resizeObj -> save($root_url.$imgname);
				}

				if($width>="800" && $width<"1000"){
					$resizeObj -> resize(600,460);
					$resizeObj -> save($root_url.$imgname);
				}
				if($width>="600" && $width<"800"){
					$resizeObj -> resize(400,260);
					$resizeObj -> save($root_url.$imgname);
				}
				
				$name = $imgname;
				//@move_uploaded_file($_FILES['memoryphoto']['tmp_name'], "memoryphoto/".$name);exit;
				$memoryphoto=" `MemoryPhoto` = '".addslashes($imgname)."', ";
				
				$resizeObj = new SimpleImage($location.$name);
				$resizeObj -> resize(160,160);
				$resizeObj -> save($root_url."thumbnail/".$name);
				$memoryphotothumb=" `MemoryPhotoThumbnail` = '".addslashes($name)."', ";
				$resizeObj -> resize(850,450);
				$resizeObj -> save($root_url."850x450/".$name);	
				$resizeObj -> resize(300,290);
				$resizeObj -> save($root_url."300x290/".$name);

				$filename = $root_url.$name;
				
				rename($location.$name, $filename);
			
			}else{
				//echo "ELSE";exit;
				$memoryphoto=" `MemoryPhoto` = 'photo_not_available.jpg', ";
				//$memoryphoto=" `MemoryPhoto` = ".'photo_not_available.jpg'.',';
				$memoryphotothumb=" `MemoryPhotoThumbnail` = 'photo_not_available.jpg', ";
			}
			
			
			$InsQuery="INSERT INTO `tbl_mymemories` 
			set `MemberID`= '".$userid."' ,
			`MemoryTitle`= '".addslashes( $title )."',
			`MemoryDate`= '".$memdate."' ,
			$memoryphoto 
			$memoryphotothumb 
			`Status`= '1' ,
			`CreatedDateTime`= NOW()";
			
			$insertid = mysql_query($InsQuery);	
			$last_insert_id = mysql_insert_id();
			
			/* Insert For Showing The Recent Activities Starts Here */
			$activity_comment="created new memory from app.";
			$Recent_Activity="INSERT INTO `tbl_recent_activity` 
			(`activity_id`, `A_MemberID`,`A_FriendID`, `Added_Page`, `Activity_Comment`, `Table_Fetch`, `PhotoId`, `Privacy`, `Status`, `createdtime`) 
			VALUES ('','".$userid."','','Memory','".$activity_comment."','memory','".$last_insert_id."','1','1',now())";
			
			$Recent_Insert = mysql_query($Recent_Activity);	
			return "MEmory has been created successfully.";
			
		}else{
			return "Database Error";
		}
	}
	
	
 }
 
?>