<?php
Class Cryptography
{
	function Encrypt($source, $destination)	{
		$key="passwordDR0wSS@P6660juht";
                $iv="password";

		if (extension_loaded('mcrypt') === true)
		{
			if (is_file($source) === true)
			{
				$source = file_get_contents($source);
				$encryptedSource=$this->TripleDesEncrypt($source,$key,$iv);
				if (file_put_contents($destination,$encryptedSource, LOCK_EX) !== false)
				{
					return true;
				}
				return false;
			}
			return false;
		}
		return false;
	}

	function Decrypt($source, $destination) {
		$key="passwordDR0wSS@P6660juht";
		$iv="password";
		if (extension_loaded('mcrypt') === true)
		{
			if (is_file($source) === true)
			{
				$source = file_get_contents($source);
    			$decryptedSource=self::TripleDesDecrypt($source,$key,$iv);
				if (file_put_contents($destination,$decryptedSource, LOCK_EX) !== false)
				{
					return true;
				}
				echo "no read";
				return false;
			}
			echo "no file";
			return false;
		}
			echo "no mcrypt";

		return false;
	}

	/*
	 Apply tripleDES algorthim for encryption, append "___EOT" to encrypted file ,
	 so that we can remove it while decrpytion also padding 0's
	 */
	function TripleDesEncrypt($buffer,$key,$iv) {

			$cipher = mcrypt_module_open(MCRYPT_3DES, '', 'cbc', '');
			$buffer.='___EOT';
			// get the amount of bytes to pad
			$extra = 8 - (strlen($buffer) % 8);
	 		// add the zero padding
			if($extra > 0) {
			for($i = 0; $i < $extra; $i++) {
				$buffer .= '_';
				}
			}
	     	 mcrypt_generic_init($cipher, $key, $iv);
		 $result = mcrypt_generic($cipher, $buffer);
		 mcrypt_generic_deinit($cipher);
		return base64_encode($result);
	}

	/*
	 Apply tripleDES algorthim for decryption, remove "___EOT" from encrypted file ,
	 so that we can get the real data.
	 */
	function TripleDesDecrypt($buffer,$key,$iv) {
	
		   $buffer= base64_decode($buffer);
		   $cipher = mcrypt_module_open(MCRYPT_3DES, '', 'cbc', '');
		   mcrypt_generic_init($cipher, $key, $iv);
		   $result = mdecrypt_generic($cipher,$buffer);
                $result=substr($result,0,strpos($result,'___EOT'));
	   	   mcrypt_generic_deinit($cipher);
	 	  return $result;
	}
}


if ($_POST) {
    
    $file = $_FILES["file"];
    $filename = $file['name'];
    $fname = trim(addslashes($filename)); // triming the name of file/image
    $fname = preg_replace('/[^a-zA-Z0-9_.]/', '_', $fname); //replace special char with _
    $fname = preg_replace('/_+/', '_', $fname);
    move_uploaded_file($file['tmp_name'], "temp/$fname");
    $filepath = "temp/".$fname;
    $obj = new Cryptography();
    $resEncrypt = $obj->Encrypt($filepath,"encryption/$fname");
    unlink($filepath);
    $encrypt_link = "<a href='http://dmbdemo.com/nikunj-test/encryption/$fname' target='_blank'>Click Here to see</a><br>OR<br><a href='http://dmbdemo.com/nikunj-test/file_upload_filter.php?decrypt=$fname'>Decrypt it!</a>";
    echo $encrypt_link;
}
if(!empty($_GET['decrypt']) && $_GET['decrypt']!=''){
    $encrypt_loc = "encryption/".$_GET['decrypt'];
    $filename = $_GET['decrypt'];
    $obj = new Cryptography();
    $resDecrypt = $obj->Decrypt($encrypt_loc,"decryption/$filename");
    $decrypt_link = "<a href='http://dmbdemo.com/nikunj-test/decryption/$filename' target='_blank'>Click Here to see</a><br>";
    echo $decrypt_link;
}

?>
<!DOCTYPE html>
<html>
    <title>Encryption Test</title>
<body>

<form method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" name="file" id="fileToUpload">
    <input type="submit" value="Upload Image" name="submit">
</form>

</body>
</html>