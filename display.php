<?php

	$file = 'data'.$_GET['user'].'.json';
	
	$str = file_get_contents($file);
	
	$json = json_decode($str, true);
	
	
	$username = $json['username'];
	$server_name = $json['server_name'];
	
	$user_id = $json['user_id'];
	$server_id = $json['server_id'];
	
	$num_list = $json['num_list'];
	$user_list = $json['user_list'];
	
?>

<head>
	<link rel='stylesheet'  href='http://192.168.0.100/libraries/css/bootstrap.min.css'>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script src="jquery.min.js"></script>
	<title>Bingo game</title>
	
	<style>
		.container{
			max-width:600px;
		}
	
		#header{
			margin: 20px auto;
			text-align: center;
			border-radius: 3px 3px 3px 3px;
			background-color:#419912 ;
			color:white;
			font-size: 50px;
            font-family: 'Raleway', sans-serif;
		}
		
		#dashboard{
			margin:-22px auto;
			border-bottom: 1px solid   #e3e3e3 ;
			border-radius: 3px 3px 3px 3px;
			height: 80px;
			padding: 2px;
		}
		
		#current-number-holder{
			border: 1px solid  #e48338 ;
			height: 75px;
			width: 75px;
			margin-top: 1px;
			text-align: center;
			border-radius: 3px 3px 3px 3px;
			background-color:red;
			display:inline-block;
			position:floating;
			float:left;
		}
		
		#current-number{
			margin: -10px 2px;
			font-size: 63px;
			color:  white;
		}
		
		#bingo-holder{
			border-radius:3px 3px 3px 3px;
			margin:1px 2px;
			height: 75px;
			width:257px;
			padding: 5px;
			display:inline-block;
			position:floating;
			float:left;
		}
		
		#bingo-holder:hover{
			border: 1px solid  #cfc1c1 ;
		}
		
		#bingo{
			margin: -18px 16px;
			font-size: 65px;
		}
		
		#bingo > span{
			display: inline-block;
			margin: 0px -8px;
		}
		
		#user-control{
			width: 170px;
			text-align:right;
			margin:auto;
			display:inline-block;
			position:floating;
			float:right;
		}
		
		#username-holder{
			text-align:center;
			border-radius:0px 0px 0px 6px ;
			width:150px;
			margin-left: 20px;
			background-color: #2c82db ;
			font-size: 14px;
			color:white;
		}
		
		#board-container{
			margin:30px 5px auto;
			width:300px;
			height:300px;
			display:inline-block;
			float:left;
		}
		
		table{
			margin-top:1px;
		}
		
		.cell-container{
			text-align:center;
			width:60px;
			height:59px;
			border:2px solid #ffee;
			border-radius:5px 5px 5px 5px;
			background-color:  #DAF7A6;
			color: #68695d ;
			font-size:20px;
		}

		.cell-clicked{
			background-color: #FFC300 ;
		}
		
		.cell-focus{
			background-color:#1bd04f;
		}
		
		#user-list{
			margin:30px 0px auto;
			width: 200px;
			height: 300px;
			display:inline-block;
			float:right;
		}
		
		#banner{
			text-align:center;
			font-size:24px;
			background-color: #008008 ;
			border-radius:3px 3px 0px 0px;
			color:white;
		}
		
		#user-list-holder{
			border:1px solid #e5f2e6;
			border-top:none;
			border-radius:2px 2px 2px 2px;
			height:260px;
			width:97%;
			margin:0px auto;
			overflow-y:scroll;
		}
		
		.player{
			border-bottom:1px solid #8b8878;
			width:98%;
			height:33px;
			margin: 2px auto;
		}
		
		.win{
			color:green;
		}
	</style>

</head>


<body>
	<div class='container'>
		<div id='header'>
			<?php echo $server_name; ?>
		</div>
		
		<div id='dashboard'>
			<div id='current-number-holder'>
				<p id='current-number'>##</p>
			</div>
			
			<div id='bingo-holder'>
				<p id='bingo'>
					<span id='B1'>B</span>
					<span id='B2'>I</span>
					<span id='B3'>N</span>
					<span id='B4'>G</span>
					<span id='B5'>O</span>
				</p>
			</div>
			
			<div id='user-control'>
				<p id='username-holder' >
					<?php echo $username; ?>
				</p>
				<a href='./leave.php' id='leave-room' class='btn btn-danger'>Leave this game</a>
			</div>
		</div>
		
		
		<div>
			<div class='container' id='board-container'>
				
			<?php
				echo '<table id="board">';
				for($i=0;$i<5;$i++){
				    echo '<tr>';
				        for($j=0;$j<5;$j++){                    
				            echo '<td class="cell-container" id="'.$num_list[$i][$j]['value'].'" data-checked="'.$num_list[$i][$j]['checked'].'">'.$num_list[$i][$j]['value'].'</td>';
				        }
				    echo '</tr>';
				}
				echo '</table>'; 
			?>
			</div>
		
			<div id='user-list'>
				<div id='banner'>
					Online users
				</div>
				
				<div id='user-list-holder'>
					<ul class='list-unstyled'>
					<?php
						foreach($user_list as $user)
							echo '<li class="player">'.$user['username'].'</li>';	
					?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</body>

<script>

	var score = 0;

	function update_board(x){
		x = '#'+x;
		$(x).click(true);
		$(x).hover(function () {
			$(this).addClass('cell-focus');
		}, function(){
			$(this).removeClass('cell-focus');
		});
	}

	function click_board(x){
		x = '#'+x;
		$(x).click(false);
		$(x).removeClass('cell-focus');
		$(x).addClass('cell-clicked');
		$(x).data('checked', '1');
	}

	function update_banner(){
		for(var i=1;i<=score;i++)
			$('#B'+i).addClass('win');
		if(score >= 5)
			$('#bingo-holder').click(true);
		else
			$('#bingo-holder').click(false);
	}

	function declare_winner(winner){
		console.log('winner is user id'+winner);
		//function to destroy everything and take user back to server page
	}
 	
	function syncJSON(){
		$user_id = <?php echo $user_id; ?>;
		$server_id = <?php echo $server_id; ?>;
		
		var num_list = new Array();

		$('#board').each(function () {
			var innerArray = [];

			$(this).find('td').each(function () {
				var x = {};
				x.value = parseInt($(this).text());
				x.checked = parseInt($(this).data('checked'));
				innerArray.push(x);
			});

			num_list.push(innerArray);
		});
		
		$.ajax({
			type: 'post',
			url: 'process.php?request=sync',
			data: {
				user_id:$user_id, 
				server_id : $server_id,
				board : JSON.stringify(num_list)
			},
			dataType: 'json',
			success:function(response, textStatus, jqXHR) {
				//console.log(response);
				var data = response;
				live(data);
				$('#current-number').html(data.current);
				score = data.score;
				update_board(data.current);
				update_banner();
			},
			error: function(jqXHR, textStatus, errorThrown){
				console.log(textStatus, errorThrown);
			}
		});
	}
	
	function live(data){
		
		var choosen = 0;
 		var turn_id = data.turn_id;
 		
		if(turn_id == <?php echo $user_id; ?>){
			
			
			$('.cell-container').hover(function () {
				if($(this).data('checked') == "0")
					$(this).addClass('cell-focus');
			}, function(){
				$(this).removeClass('cell-focus');
			});
			
			$(document).on("click", "td", function() {
				choosen = parseInt($( this ).text());
				document.getElementById('current-number').innerHTML = choosen;
				
				$user_id = <?php echo $user_id; ?>;
				$server_id = <?php echo $server_id; ?>;
				
				$.ajax({
					type: 'POST',
					url: 'process.php?request=myturn',
					data: {
						user_id : $user_id,
						server_id : $server_id,
						current : choosen
					},
					cache: false,
					dataType: 'json',
					success:function(response, textStatus, jqXHR) {
						//console.log(response);
						click_board(response.current);
					},
					error: function(jqXHR, textStatus, errorThrown){
						console.log(textStatus, errorThrown);
					}
				});
			});
		}else{
			$('.cell-container').click(function(){
				var choosen = parseInt($(this).html());
				click_board(choosen);
			});
		}
	}

	function claim_win(){
		$user_id = <?php echo $user_id; ?>;
		$server_id = <?php echo $server_id; ?>;

		$.ajax({
			type: 'POST',
			url: 'process.php?request=winner',
			data: {
				user_id : $user_id,
				server_id : $server_id
			},
			cache: false,
			dataType: 'json',
			success:function(response, textStatus, jqXHR) {
				//console.log(response);
				if(response == true)
					alert('You win.');
			},
			error: function(jqXHR, textStatus, errorThrown){
				console.log(textStatus, errorThrown);
			}
		});

	}

	$('#bingo-holder').click(function (){
		claim_win();
	});
		
	setInterval(function(){ syncJSON(); }, 500);

</script>
