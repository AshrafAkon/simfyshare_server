<?php

include 'auth.php';

header("Content-Encoding: none");
$return_data = array(
	"code" => false,
);

if (isset($_POST['security_key'])) {
	if ($_POST['security_key'] == $security_key) {

		if (isset($_POST['data_key'])) {

			// starting pdo mysql database connection
			$dbc = new PDO('mysql:host=' . $servername . '; dbname=' . $dbname, $username, $password);
			if ($stmt = $dbc->prepare("SELECT file_basic_info from info_dict_table where data_key = :data_key")) {

				$stmt->bindParam(":data_key", $_POST['data_key']);
				if ($stmt->execute()) {
					$return_data['code'] = true;
				}
				//echo json_encode($stmt->fetch(PDO::FETCH_COLUMN));
				//$return_data['data'] = $stmt->fetch(PDO::FETCH_COLUMN);

				$return_data['data'] = $stmt->fetch(PDO::FETCH_COLUMN);
			}
		}
		//echo json_encode($return_data);
		echo $return_data['data'];
	}
}

?>