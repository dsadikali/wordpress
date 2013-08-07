<div class="wrap">
	
    <?php    echo "<h2>" . __( 'Video Upload Form' ) . "</h2>"; 
	
    $s3_options = get_option('s3plugin_options');
	$s3key = $s3_options["s3access_string"]; 
	$s3secret = $s3_options["s3secret_string"]; 
	$s3bucket = $s3_options["admin_s3bucket_dropdown"];   
	
	//include the S3 class
			if (!class_exists('S3'))require_once('S3.php');
		
			
			//instantiate the class
			$s3 = new S3($s3key, $s3secret);
			
			//check whether a form was submitted
			if(isset($_POST['Submit'])){
			
				//retreive post variables
				$fileName = $_FILES['theFile']['name'];
				$fileTempName = $_FILES['theFile']['tmp_name'];
				
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

<h1>Upload a file</h1>
<p>Please select a file by clicking the 'Browse' button and press 'Upload' to start uploading your file.</p>
   	<form action="" method="post" enctype="multipart/form-data" name="form1" id="form1">
      <input name="theFile" type="file" />
      <input name="Submit" type="submit" value="Upload">
	</form>
</div>