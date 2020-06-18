<?php
include './auth.php';

header("Content-Type: application/json");
$return_data = array(
	"code" => false,
	"data" => []
);

if ($_POST['security_key'] == $security_key) {
	if (isset($_POST['data_key'])) {
		/*
		 * returns a list of already available uploads of data_key
		 * retuns an empty list if no file is uplaoded with data_key
		 */

		$dbc = new PDO('mysql:host=' . $servername . '; dbname=' . $dbname, $username, $password);

		if ($stmt = $dbc->prepare("SELECT file_name FROM time WHERE data_key = :data_key")) {
			$stmt->bindParam(':data_key', $data_key);
			$data_key = $_POST['data_key'];
			if ($stmt->execute()) {
				$return_data['code'] = true;
				$return_data['data'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
			}
		}
	}
	echo json_encode($return_data);
}
