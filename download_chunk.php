<?php
include 'auth.php';

//global headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
// $post_json = file_get_contents("php://input");
// $_POST = json_decode($post_json, true);
//var_dump($_POST);
if ($_POST['security_key'] == $security_key) {
	//echo $_POST['file_name'];
	if (isset($_POST['file_name']) and isset($_POST['data_key'])) {
		$dbc = new PDO('mysql:host=' . $servername . '; dbname=' . $dbname, $username, $password);

		// sending info_dict to user
		if ($stmt = $dbc->prepare("SELECT data_key from time where file_name = :file_name")) {

			$stmt->bindParam(":file_name", $_POST['file_name']);
			$stmt->execute();

			if ($stmt->fetch(PDO::FETCH_COLUMN) == $_POST['data_key'])
			##$return_data['data'] = $stmt->fetch(PDO::FETCH_COLUMN);

			{
				$return_data['code'] = true;
				$file                = $main_upload_dir . $_POST['file_name'];

				//echo "authincated";
				if (file_exists($file)) {
					//echo "file exists";
					if (isset($_POST['return_file'])) {

						// this headers are required or client
						// will not understand the file download properly
						header('Content-Description: File Transfer');
						header('Content-Type: application/octet-stream');
						header('Content-Disposition: attachment; filename="' . basename($file) . '"');
						header('Expires: 0');
						header('Cache-Control: must-revalidate');
						header('Pragma: public');
						header('Content-Length: ' . filesize($file));
						// header content-encoding is set to be none.
						// otherwise xmlhttprequest.onprogress will
						// set onprogress event.lengthcompuatable to false
						// see more at : https://stackoverflow.com/a/49580828
						header("Content-Encoding: none");
						readfile($file);
						exit;
					}

				}
			}
			#echo "false";
		}
	}

}
?>