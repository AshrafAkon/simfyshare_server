<?php

include 'auth.php';
//and ($_FILES['split_file']['error'] == 1)

//header("Access-Control-Allow-Origin: *");
//header("Access-Control-Allow-Headers: Content-Type");
//header("Content-Type: application/json");
//$post_json = file_get_contents("php://input");
//$_POST = json_decode($post_json, true);
//echo var_dump($_FILES);

//echo var_dump($_POST);
$return_data = array(
	"code"        => false,
	'file_exists' => false,
);
if ($_POST['security_key'] == $security_key) {

	if (isset($_FILES['split_file']) and ($_FILES['split_file']['error'] == 0) and isset($_POST['file_name']) and
		isset($_POST['data_key'])) {
		/*
		 * adding an entry to database and uploading the file
		 */

		$dbc = new PDO('mysql:host=' . $servername . '; dbname=' . $dbname, $username, $password);

		if ($stmt_to_entry_data = $dbc->prepare("INSERT INTO time (file_name, data_key) VALUES( :file_name, :data_key)")) {
			$stmt_to_entry_data->bindParam(':data_key', $_POST['data_key']);
			$stmt_to_entry_data->bindParam(':file_name', $_POST['file_name']);
			if ($stmt_to_entry_data->execute()) {
				$return_data['code'] = true;
			}

			$file_temp   = $_FILES['split_file']['tmp_name'];
			$target_file = $main_upload_dir . strval($_POST['file_name']);

			if (!file_exists($target_file)) {

				if ($_FILES['split_file']['size'] > 0) {
					move_uploaded_file($file_temp, $target_file);
				}

			} else {
				$return_data['file_exists'] = true;
			}

		}

	}
	echo json_encode($return_data);

}
?>