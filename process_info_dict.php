<?php

include 'auth.php';

header("Content-Encoding: none");

$return_data = array(
	"code" => false,
	"data" => array(),
);

function verify_info_dict_private_key($dbc, $data_key, $info_dict_private_key) {
	$stmt = $dbc->prepare("SELECT info_dict_private_key FROM info_dict_table
					 WHERE data_key = :data_key");
	$stmt->bindParam(":data_key", $data_key);
	$stmt->execute();
	if ($stmt->fetch(PDO::FETCH_COLUMN) == $info_dict_private_key) {
		return True;
	} else {
		return False;
	}

}

if (isset($_POST['security_key'])) {
	if ($_POST['security_key'] == $security_key) {

		if (isset($_POST['data_key'])) {

			// starting pdo mysql database connection
			$dbc = new PDO('mysql:host=' . $servername . '; dbname=' . $dbname, $username, $password);
			/*
			 *if download_info_dict is set to true
			 * then it will give back info_dict
			 * associated with data_key
			 */

			if (isset($_POST['download_info_dict'])) {
				if ($_POST['download_info_dict'] == "true") {
					// sending info_dict to user

					if ($stmt = $dbc->prepare("SELECT info_dict from info_dict_table where data_key = :data_key")) {

						$stmt->bindParam(":data_key", $_POST['data_key']);
						if ($stmt->execute()) {
							$return_data['code'] = true;
						}
						//echo json_encode($stmt->fetch(PDO::FETCH_COLUMN));
						//$return_data['data'] = $stmt->fetch(PDO::FETCH_COLUMN);

						$return_data['data'] = $stmt->fetch(PDO::FETCH_COLUMN);
						//var_dump($return_data);
					}
					if ($stmt = $dbc->prepare("SELECT file_name FROM time WHERE data_key=:data_key")) {

						$stmt->bindParam(":data_key", $_POST['data_key']);

						if (!($stmt->execute())) {
							$return_data['code'] = false;
						}

						$file_list = $stmt->fetchAll(PDO::FETCH_COLUMN);
						//var_dump($file_list);
						$return_data['all_file_available'] = true;
						for ($x = count($file_list) - 1; $x >= 0; $x--) {
							if (!(file_exists($main_upload_dir . $file_list[$x]))) {
								$return_data['all_file_available'] = false;
							}
						}
						if ($return_data['all_file_available'] == true) {
							echo ($return_data['data']);

						}

					}
					exit;

				}
			} else if (
				isset($_POST['set_info_dict']) and isset($_POST['info_dict_private_key'])
			) {
				/*
				 * if set_info_dict is set to true then
				 * it will receive a info_dict_private_key
				 * and initialize a field to store info_dict.
				 * it should be always called first
				 * returns true(1) if executed successfull
				 * otherwise false(0)
				 */
				try {
					if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
						$ip_address = $_SERVER['HTTP_CLIENT_IP'];
					} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
						$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
					} else {
						$ip_address = $_SERVER['REMOTE_ADDR'];
					}

				} catch (Exception $e) {
					$ip_address = null;

				}
				// echo $ip_address;
				// echo $user_agent;

				$stmt = $dbc->prepare("INSERT INTO info_dict_table(data_key,
                                    info_dict_private_key, ip, user_agent) VALUES(:data_key,
									:info_dict_private_key, :ip_addr, :user_agent)");

				$stmt->bindParam(':data_key', $_POST['data_key']);
				$stmt->bindParam(':info_dict_private_key', $_POST['info_dict_private_key']);
				$stmt->bindParam(':ip_addr', $ip_address);
				$stmt->bindParam(':user_agent', $_SERVER['HTTP_USER_AGENT']);

				if ($stmt->execute()) {
					$return_data['code'] = true;
				} else {
					$give_data_key = $dbc->prepare("SELECT data_key FROM info_dict_Table
									WHERE info_dict_private_key = :info_dict_private_key");
					$give_data_key->bindParam(':info_dict_private_key', $_POST['info_dict_private_key']);
					$give_data_key->execute();

					if ($give_data_key->fetch(PDO::FETCH_COLUMN) == $_POST['data_key']) {
						$return_data['code'] = true;
					}
				}

			} else if (isset($_POST['upload_info_dict']) and isset($_FILES['file_basic_info'])
				and isset($_POST['info_dict_private_key']) and isset($_FILES['info_dict'])) {
				if ($_POST['upload_info_dict'] == 'true') {
					//strlen($_FILES['info_dict']) > 0

					/*
					 * inserts info_dict with specified data_key
					 * and info_dict_private_key
					 * returns true(1) if successful
					 * returns false(0) if fails.
					 */

					if ($stmt = $dbc->prepare("UPDATE info_dict_table SET info_dict
								= :info_dict, file_basic_info = :file_basic_info WHERE data_key = :data_key
								AND info_dict_private_key = :info_dict_private_key")) {

						$t_info  = fopen($_FILES['info_dict']['tmp_name'], 'rb');
						$t_basic = fopen($_FILES['file_basic_info']['tmp_name'], 'rb');
						$stmt->bindParam(':data_key', $_POST['data_key']);
						$stmt->bindParam(':info_dict', $t_info, PDO::PARAM_LOB);
						$stmt->bindParam(':file_basic_info', $t_basic, PDO::PARAM_LOB);
						$stmt->bindParam(':info_dict_private_key', $_POST['info_dict_private_key']);

						if ($stmt->execute()) {
							$return_data['code'] = true;
						}

					}

				}
			}
		}
		echo json_encode($return_data);
		//var_dump($return_data);
	}
}
?>