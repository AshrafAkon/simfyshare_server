<?php
include './auth.php';

// file should accept the private_key_of_info dict

$return_data = array(
	"code" => false,
);

if ($_POST['security_key'] == $security_key) {
	if (isset($_POST['data_key']) and isset($_POST['file_name'])) {
		#echo $_POST['data_key'];
		$dbc = new PDO('mysql:host=' . $servername . '; dbname=' . $dbname, $username, $password);

		if ($stmt = $dbc->prepare("SELECT data_key FROM time WHERE file_name = :file_name")) {

			$stmt->bindParam(":file_name", $_POST['file_name']);
			if ($stmt->execute()) {
				$return_data['code'] = true;
			}
			$stmt->bind_result($got_data_key);

			if ($stmt->fetch()) {
				if ($got_data_key == $_POST['data_key'] and file_exists($main_upload_dir . $_POST['file_name'])) {
					echo "True";
				} else {
					echo "False";
				}

			} else {
				echo "False";
			}

		}

	} else if (isset($_POST['data_key'])) {
		// echo all available file list
		#echo $_POST['data_key'];
		$conn = new mysqli($servername, $username, $password, $dbname);
		if ($conn->connect_error) {
			die("connection failed: " . $conn->connect_error);
		}
		#to count from table time : $stmt = $conn->prepare("SELECT COUNT(1) FROM time WHERE data_key=?")
		if ($stmt = $conn->prepare("SELECT info_dict FROM info_dict_table WHERE data_key=?")) {

			$stmt->bind_param("s", $data_key);
			$data_key = $_POST['data_key'];
			$stmt->execute();

			$stmt->bind_result($info_dict_from_db);

			//for($x = 0; $x )
			if ($stmt->fetch()) {
				//echo $got_count;
				$json_info_dict   = json_decode($info_dict_from_db, true);
				$available_chunks = 0;
				for ($x = 0; $x < $json_info_dict['chunk_count']; $x++) {
					$file = $main_upload_dir . $json_info_dict['file_serial'][$x];
					//echo "authincated";
					if (file_exists($file)) {
						$available_chunks += 1;
					}
				}
				echo $available_chunks;
			} else {
				echo 0;
			}

			$stmt->close();
		}
		$conn->close();
	}
	echo json_encode($return_data);
}
?>
