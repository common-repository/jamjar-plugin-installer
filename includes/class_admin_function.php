<?php
if ( ! defined( 'ABSPATH' ) )
		exit; // Exit if accessed directly

if ( ! class_exists( 'ClassJamjarAdminFunction' ) ) {
	//require_once(ABSPATH .'/wp-admin/includes/plugin.php');

	class ClassJamjarAdminFunction{

		public $getpath;
		public $zipinstance;
		public $plugindir;

		function __construct(){
			$this->zipinstance = new ZipArchive();
			$this->init();
			$this->plugindir = realpath(dirname(plugin_dir_path(__DIR__)))."/";
		}

		public function doAjaxPost(){
			//potential future update
		}

		public function init(){		

	
			add_action( 'wp_ajax_jj_plugin_upload',array($this,'uploadResponse'));
		}

		public function doPluginUpload($autoactivate){

			$dir = $this->plugindir;

			
			$n = 1;
			$f = $_FILES["file"];
			if($f["name"]) {
				$filename = $f["name"];
				$source = $f["tmp_name"];
				$type = $f["type"];
				
				$name = explode(".", $filename);
				$accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
				foreach($accepted_types as $mime_type) {
					if($mime_type == $type) {
						$okay = true;
						break;
					} 
				}
				
				$foldername = $name[0];		

				$continue = strtolower(end($name)) == 'zip' ? true : false;

				if(!$continue) {
					//header($_SERVER['SERVER_PROTOCOL'] . '400 Bad Request', true, 400);
					$message["message"] = "The file you are trying to upload is not a .zip file. Please try again.";
					$message["success"] = 0;
				}else{

					$target_path = $dir.$filename;  // change this to the correct site path
		

					if(move_uploaded_file($source, $target_path)) {

						
					if(!$this->pluginInspect($target_path)){
					
						$message["message"] = "Your .zip file is not a valid plugin.";
						$message["success"] = 0;
						unlink($target_path);
						return $message;	
					}

						$zip = $this->zipinstance;
						$x = $zip->open($target_path);
						if ($x) {
							$zip->extractTo($dir); 
							$zip->close();
					
							unlink($target_path);
						}
						

					    if(!$this->activatePlugin($dir.$foldername,$check,true)){
					    	rmdir($dir.$foldername);
					    	$message["message"] = "Your .zip plugin file is corrupted.";
							$message["success"] = 0;
					    }else{
					    	if($autoactivate)
					    		$this->activatePlugin($dir.$foldername);
					    	$message["message"] = "Your plugin(.zip) was uploaded and installed.";
							$message["success"] = 1;
					    }
						
					} else {	
						$message["message"] = "There was a problem with the upload. Please try again.";
						$message["success"] = 0;
					}

				}



			
			}

			return $message;
		
		}

		public function uploadResponse(){

			if($_POST['intention'] == "autoactivate"){
				$response = $this->doPluginUpload(true);
			}else{
				$response = $this->doPluginUpload(false);
			}
			

			if(!(int)$response["success"]){
				header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', false, 400);
				echo $response["message"];
			}else{
				echo $response["message"];
			}
			die();
			
		}

		public function activatePlugin($path,$check = false){

			foreach(glob($path."/*.php") as $phpfiles){
				$contents = file_get_contents($phpfiles);
				if(strpos($contents,"Plugin Name") !== false){
					$main_file = $phpfiles;
					if(!$check)
						activate_plugin($main_file);
					return $is_activated = 1;	
				}
			}

			return $is_activated = 0;

		}

		public function pluginInspect($path){
			$zip = zip_open($path);
			$itis = 0;
			if ($zip)
			  {
			  while ($zip_entry = zip_read($zip))
			    {
			    if(preg_match("/\.php$/",zip_entry_name($zip_entry))){
			    	if (zip_entry_open($zip, $zip_entry))
				      {
				     	if(strpos(zip_entry_read($zip_entry),"Plugin Name") !== false){
				     		$itis = 1;
				     		zip_entry_close($zip_entry);
				     		break;
				     	}
				      	zip_entry_close($zip_entry);
				      }
			    }
			   
			    
			  }

			zip_close($zip);
			return $itis;
			}
		}

		public function checkPluginFolderPermission(){
		
			if(!is_writable($this->plugindir))
				echo "<div class='dir-perm-error'>The plugin directory is not writable , please make sure it is writable.</div>";
			
		}

	}

	$admininstance = new ClassJamjarAdminFunction();

}