    <?php 
	
    $s3_options = get_option('s3plugin_options');
	$s3key = $s3_options["s3access_string"]; 
	$s3secret = $s3_options["s3secret_string"]; 
	$s3bucket = $s3_options["s3bucket_dropdown"];   
	
	//include the S3 class
			if (!class_exists('S3'))require_once('S3.php');
		
			
			//instantiate the class
			$s3 = new S3($s3key, $s3secret);
			
			//check whether a form was submitted
			if(isset($_POST['Submit'])){
			
				//retreive post variables
				$fileName = $_FILES['s3filename']['name'];
				$fileTempName = $_FILES['s3filename']['tmp_name'];
				
				//create a new bucket
				$s3->putBucket("$s3bucket", S3::ACL_PUBLIC_READ);
				
				//move the file
				if ($s3->putObjectFile($fileTempName, "$s3bucket", $fileName, S3::ACL_PUBLIC_READ))

                               
{	   
					echo "<div id='setting-error-settings_updated' class='updated settings-error'><strong>Thank you, your file was successfully uploaded.</strong></div>";
					
				}else{
					echo "<div id='setting-error-settings_updated' class='updated settings-error'><strong>Something went wrong while uploading your file... sorry please try again.</strong></div>";
				}
			}
		?>

   	<form action="" method="post" enctype="multipart/form-data" name="form1" id="form1">
      <input name="s3filename" type="file" />
      <input name="Submit" type="submit" value="Upload">
	</form>

