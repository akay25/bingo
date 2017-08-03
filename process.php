<?php


if(isset($_GET['request'])):
	
	require_once('function.php');
	
	switch($_GET['request']):
		case 'sync':
			$user_id = $_SESSION['user_id'];
			$server_id = $_SESSION['server_id'];
			$board = json_decode($_POST['board'])[0];

			$file = 'sync'.$server_id.'.json';
			$sync = file_get_contents($file);
			$sync = json_decode($sync);
			
			//Converting board[1..25] to 2-D matrix num_list
			$num_list = [];
			$k = 0;
			for($i=0;$i<5;$i++){
				$row = [];
				for($j=0;$j<5;$j++){
					$x = $board[$k]->checked;
					$k++;
					array_push($row, $x);
				}
				array_push($num_list, $row);
			}

			//print_r($board);
			//print_r($num_list);
			$score = 0;

			//Checking for crossed rows, columns and diagonals
			$sum = 0;
			for($i=0;$i<5;$i++)
				for($j=0;$j<5;$j++)
					if($i == $j && $num_list[$i][$j] == 1)
						$sum++;
		
			if($sum == 5)
				$score++;
			
			$sum = 0;
			for($i=0;$i<5;$i++)
				for($j=0;$j<5;$j++)
					if(($i+$j+1)==5 && $num_list[$i][$j] == 1)
						$sum++;

			if($sum == 5)
				$score++;
			
			for($i=0;$i<5;$i++){
				$sum = 0;
				for($j=0;$j<5;$j++){
					if($num_list[$i][$j] == 1)
						$sum++;
				}
				if($sum == 5)
					$score++;
				$sum = 0;
			}

			for($i=0;$i<5;$i++){
				$sum = 0;
				for($j=0;$j<5;$j++){
					if($num_list[$j][$i] == 1)
						$sum++;
				}
				if($sum == 5)
					$score++;
				$sum = 0;
			}

			if($score >= 5)
				$sync->winner = $user_id;
				
			if($sync->played == 1 && $sync->turn_id != $user_id){
				$sync->turn_id++;
				if($sync->turn_id > $sync->total)
					$sync->turn_id = 1;
				$sync->played = 0;
			}

			
			$fhandle = fopen($file, 'w') or die('Cannot open the file.');
			$str = json_encode($sync);
			fwrite($fhandle, $str);
			fclose($fhandle);
			
			$sync->score = $score;
			
			$json = $sync;
		break;
		
		case 'myturn':
			$user_id = $_SESSION['user_id'];
			$current = $_POST['current'];
			$server_id = $_SESSION['server_id'];

			$file = 'sync'.$server_id.'.json';
			$json = file_get_contents($file);
			$json = json_decode($json);

			if($user_id == $json->turn_id){
			
				$num_list = file_get_contents('num_list'.$server_id.'.json');
				$num_list = json_decode($num_list);

				//print_r($num_list);
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
					$fhandle = fopen('num_list'.$server_id.'.json', 'w') or die('Cannot open the file.');
					$str = json_encode($num_list);
					fwrite($fhandle, $str);
					fclose($fhandle);
					
					//Tell the database that player played his move
					$json->played = 1;
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
			$user_id = $_SESSION['user_id'];
			$server_id = $_SESSION['server_id'];
			
			
			$file = 'sync'.$server_id.'.json';
			$sync = file_get_contents($file);
			$sync = json_decode($sync);

			//print_r($sync);

			if($sync->winner == $user_id)
				$json = true;
			else
				$json = false;
		break;
		
		case 'register_user':
			$username = $_POST['username'];

			$res = $db->insert('users', 'username, server_id', '"'.$username.'", 0');
			if($res > 0){
				$json = true;
				$user = $db->select('users', 'id',  "username ='$username'");
				
				$_SESSION['username'] = $username;
				$_SESSION['user_id'] = $user[0]['id'];
			}else
				$json = false;
		break;
		
		case 'register_server':
			$creator = $_SESSION['username'];
			$servername = $_POST['servername'];
			$user_id = $_SESSION['user_id'];
			
			$res = $db->insert('server', 'name, creator, user_count, is_live, is_running, user_list', "'$servername', '$creator', 1, 1, 0, '$user_id'");
			if($res > 0){
				
				$server = $db->select('server', 'id', "name='$servername'")[0];

				$server_id = $server['id'];
				$board = '[{"value":1,"checked":0},{"value":2,"checked":0},{"value":3,"checked":0},{"value":4,"checked":0},{"value":5,"checked":0},{"value":6,"checked":0},{"value":7,"checked":0},{"value":8,"checked":0},{"value":9,"checked":0},{"value":10,"checked":0},{"value":11,"checked":0},{"value":12,"checked":0},{"value":13,"checked":0},{"value":14,"checked":0},{"value":15,"checked":0},{"value":16,"checked":0},{"value":17,"checked":0},{"value":18,"checked":0},{"value":19,"checked":0},{"value":20,"checked":0},{"value":21,"checked":0},{"value":22,"checked":0},{"value":23,"checked":0},{"value":24,"checked":0},{"value":25,"checked":0}]';
				
				$fhandle = fopen('num_list'.$server_id.'.json', 'w') or die('Cannot open the file.');
				fwrite($fhandle, $board);
				fclose($fhandle);
				
				$sync = '{"server_id":'.$server_id.',"turn_id":1,"total":1,"current":"##","winner":0,"played":0}';
			
				$fhandle = fopen('sync'.$server_id.'.json', 'w') or die('Cannot open the file.');
				fwrite($fhandle, $sync);
				fclose($fhandle);
				
				$_SESSION['server_name'] = $servername;
				$_SESSION['server_id'] = $server['id'];
				$_SESSION['creator'] = $creator;
				$json = true;
			}else
				$json = false;
		break;
		
		case 'get_list':
			$username = $_SESSION['username'];
			$user_id = $_SESSION['user_id'];
			
			$servers = $db->select('server', '*',  "is_live = 1 AND is_running = 0");
			
			$json = $servers;
		
		break;
		
		case 'join_server':
			$server_id = $_POST['server'];
			$user_id = $_SESSION['user_id'];
			
			$server = $db->select('server', '*', "id = '$server_id'")[0];
			
			if(count($server)){
				$user_list = explode(',', $server['user_list']);
				array_push($user_list, $user_id);
				$user_str = implode(',', $user_list);
				$count = count($server);
				$res = $db->update('server', "user_list='$user_str', user_count=$count", "id = $server_id");
				if($res > 0){
					$file = 'sync'.$server_id.'.json';
					$sync = file_get_contents($file);
					$sync = json_decode($sync);
				
					$sync->total++;
			
					$fhandle = fopen('sync'.$server_id.'.json', 'w') or die('Cannot open the file.');
					$str = json_encode($sync);
					fwrite($fhandle, $str);
					fclose($fhandle);
				
					//Set server session
					$json = true;
					$_SESSION['server_name'] = $server['name'];
					$_SESSION['server_id'] = $server_id;
					$_SESSION['creator'] = $server['creator'];
				}else
					$json = false;
			}else
				$json = false;
			
		break;
		
		case 'update_list':
			$server_id = $_SESSION['server_id'];
			
			$server = $db->select('server', '*', "id = $server_id")[0];
			
			if($server['is_running'] == 1)
				$json = array('is_running'=>true);
			else{
				$user_list = explode(',', $server['user_list']);
			
				$list = [];
				foreach($user_list as $user){
					$username = $db->select('users', 'username', "id = $user")[0]['username'];
				
					array_push($list, $username);
				}
				$json = $list;
			}	
		break;
		
		case 'start':
			$server_id = $_SESSION['server_id'];
			$username = $_SESSION['username'];
			$creator = $_SESSION['creator'];
			
			if($creator != $username)
				$json = false;
			else{
				$server = $db->select('server', '*', "id = $server_id")[0];
				$user_list = explode(',', $server['user_list']);
				$turn_id = 0;
				$turn = $user_list[$turn_id];		
				
				$res = $db->update('server', "turn_id=$turn_id,turn=$turn, is_running=1", "id = $server_id");
				if($res > 0)
					$json = true;
				else
					$json = false;
			}
			
		break;
	endswitch;
endif;

header('Content-type: application/json');
echo json_encode( $json );
