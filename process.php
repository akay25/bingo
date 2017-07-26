<?php


if(isset($_GET['request'])):
	
	switch($_GET['request']):
		case 'sync':
			$file = 'sync.json';
			$json = file_get_contents($file);
		break;
		case 'myturn':
			$user_id = $_POST['user_id'];
			$current = $_POST['current'];
			$server_id = $_POST['server_id'];

			$file = 'sync.json';
			$json = file_get_contents($file);
			$json = json_decode($json);

			if($user_id == $json->turn_id){
			
				$num_list = file_get_contents('num_list.json');
				$num_list = json_decode($num_list);

				$flag = false;
			
				foreach($num_list as $row)
					if($row->value == $current)
						if($row->checked == 0){
							$row->checked = 1;
							$flag = true;
						}
							
					//print_r($num_list);

				if($flag == true){
					//number entered successfully
					$fhandle = fopen('num_list.json', 'w') or die('Cannot open the file.');
					$str = json_encode($num_list);
					fwrite($fhandle, $str);
					fclose($fhandle);
				
				
					$json->turn_id++;
					if($json->turn_id > $json->total)
						$json->turn_id = 1;
					$json->current = $current;
					

					$fhandle = fopen($file, 'w') or die('Cannot open the file.');
					$str = json_encode($json);
					fwrite($fhandle, $str);
					fclose($fhandle);
				}
				else
					$json = '{"error":"Number is not in the board or already checked."}';
			}else	
				$json = '{"error":"Not your turn."}';
		break;
		case 'winner':
			$user_id = $_POST['user_id'];
			$server_id = $_POST['server_id'];
			$board = $_POST['board'];
		break;
	endswitch;
endif;

header('Content-type: application/json');
echo json_encode( $json );
