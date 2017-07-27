<?php


if(isset($_GET['request'])):
	
	switch($_GET['request']):
		case 'sync':
			$user_id = $_POST['user_id'];
			$server_id = $_POST['server_id'];
			$board = json_decode($_POST['board'])[0];

			$file = 'sync.json';
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
			$user_id = $_POST['user_id'];
			$server_id = $_POST['server_id'];
			
			
			$file = 'sync.json';
			$sync = file_get_contents($file);
			$sync = json_decode($sync);

			//print_r($sync);

			if($sync->winner == $user_id)
				$json = true;
			else
				$json = false;
		break;
	endswitch;
endif;

//header('Content-type: application/json');
echo json_encode( $json );
