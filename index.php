<?php require_once('function.php'); 
	
	if(isset($_SESSION['username'], $_SESSION['user_id']) && !empty($_SESSION['username']) && !empty($_SESSION['user_id'])){
		$username = $_SESSION['username'];
		$user_id = $_SESSION['user_id'];
	}

	if(isset($_SESSION['server_name'], $_SESSION['server_id']) && !empty($_SESSION['server_name']) && !empty($_SESSION['server_id'])){
		$server_name = $_SESSION['server_name'];
		$server_id = $_SESSION['server_id'];
		$creator = $_SESSION['creator'];
	}
?>
<html>
    <head>
        <link rel="stylesheet" href="./css/main.css" type="text/css">
        <link rel='stylesheet'  href='http://192.168.0.100/libraries/css/bootstrap.min.css'>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<script src="jquery.min.js"></script>
		<script src="http://192.168.0.100/libraries/js/tether.min.js"></script>
		<script src="http://192.168.0.100/libraries/js/bootstrap.min.js"></script>
        <title>BINGO</title>        
        <style>
        	.upper-body{
                 margin-top:30px;
                 padding:20px;
                 font-size: 90px;
                 font-family: 'Raleway', sans-serif;
                 text-align:center;
            }

          	.box{
                 margin-top: 30px;
                 padding: 10px;
           	}
           	
           	.container{
           		max-width:680px;
           	}
           	  	
           	#response{
               font-family: 'Raleway', sans-serif;
               text-align: center;
               font-size: 20px;
           	}
           	
           	#player_list, #server_list{
           		margin: 0 auto;
           		width:400px;
           	}
        </style>
    </head>
    <body>
    	<center>
    		<div class='container'>
        	<div class="col-md-12 upper-body">
                 BINGO
            </div>

            <div class="box">
            	<div class="row">
		            <?php
		            
		            if(!isset($username, $user_id)){?>
		            <div class="input-group" id='username-holder'>
		            	<input type="text" class="form-control" id="user_id" placeholder="Enter your name ">
				        <span class="input-group-btn">
				        	<button class="btn btn-primary" id="submit">Create ID</button>
				        </span>
		            </div>
                    <?php } 
                    
                    if(!isset($server_name, $server_id)){?>
            
                    <div class="input-group" id='server-holder'>
		                <input type="text" class="form-control" id="server_name" placeholder="Enter playground name">
				            <span class="input-group-btn">
				            	<button class="btn btn-primary" id="create-server">Create playground</button>	
				            </span>
		                <p><h2>OR</h2></p>
		                <button class="btn btn-primary" id="join-server">Join playground</button>
                    </div>
                    <?php } ?>
                </div>
       		</div>
		   	<div id="response">
		   		<?php if(isset($username)) echo 'Hello '. $username; ?>
		   	</div>
		   </div>
       </center>
       
       <div id="player_list" class="modal fade" role="dialog">
		   <div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 id='server-name'><?php if(isset($server_name))echo $server_name; ?></h4>
				  	</div>
				  	<div class="modal-body">
				  
				  		<!---List of users will be displayed here -->
						<h5>Playground members</h5>
				  		<ul id='list'>
				  		</ul>
				  	</div>
				  	<div class="modal-footer">
				  	
				  	<?php
				  		if(isset($creator) && $creator == $username)
				  			echo '<button id="start-game" type="button" class="btn btn-success">Start game</button>';
				  	?>
						<button id="leave" type="button" class="btn btn-danger" data-dismiss="modal">Leave this playground</button>
				  	
				  	</div>
				</div>
			</div>
		</div>
		
		<div id="server_list" class="modal fade" role="dialog">
		   <div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 id='server-name'>Available playground list</h4>
				  	</div>
				  	<div class="modal-body">
				  		<!---List of users will be displayed here -->
						<h5 id='playground-header'>Select your playground</h5>
				  		<select id='server-selected'>
				  			
				  		</select>
				  	</div>
				  	<div class="modal-footer">
				  	
				  		<button id="join-game" type="button" class="btn btn-success">Join playground</button>
				  	</div>
				</div>
			</div>
		</div>
    </body>
    <script>
    
    	$(document).ready(function(){
   			<?php if(!isset($_SESSION['username']) || empty($_SESSION['username'])){ ?>
       		$('#server-holder').hide();
       		<?php } ?>
       		<?php if(isset($_SESSION['server_name']) || !empty($_SESSION['server_name'])){ ?>
       		show_server();
       		<?php } ?>
    	});
    	
    	$('#submit').click(function (){
    		var name = $('#user_id').val();


			if(name.length < 4 || !name.match(/^[a-zA-Z0-9]+$/))
				$('#response').html('Enter a valid name');
			else
				$.ajax({
					type: 'POST',
					url: 'process.php?request=register_user',
					data: {
						username : name,
					},
					cache: false,
					dataType: 'json',
					success:function(response, textStatus, jqXHR) {
						//console.log('response:'+response);
						if(response == true){
							$('#username-holder').hide();
							$('#server-holder').show();
							$('#response').html('Username created.');
						}else
							$('#response').html('Try another username.');
					},
					error: function(jqXHR, textStatus, errorThrown){
						console.log(textStatus, errorThrown);
					}
				});
		});
		
		$('#join-server').click(function (){
			$('#join-game').prop('disabled', false);
			$.ajax({
				type: 'POST',
				url: 'process.php?request=get_list',
				cache: false,
				dataType: 'json',
				success:function(response, textStatus, jqXHR) {
					//console.log(response);
					if(response.length == 0){
						$('#playground-header').html("It looks like no one has created any playground yet or every playground is running the game right now. Either create your own playground or wait.");
						$('#join-game').prop('disabled', true);
					}
					var list = $("#server-selected");
					var parent = list.parent();

					list.detach().empty().each(function(i){
						for (var x = 0; x < response.length; x++){
							$(this).append('<option value = ' + response[x].id + '>' + response[x].name + ' - ' + response[x].creator + '</option>');
							if (x == response.length - 1){
								$(this).appendTo(parent);
							}
						}
					});
					$('#server_list').modal('toggle');
				},
				error: function(jqXHR, textStatus, errorThrown){
					console.log(textStatus, errorThrown);
				}
			});
		});
		
		$('#join-game').click(function(){
			var server_id = $('#server-selected').find(":selected").val();
			if(server_id > 0){
				$.ajax({
					type: 'POST',
					url: 'process.php?request=join_server',
					data: {
						server : server_id,
					},
					cache: false,
					dataType: 'json',
					success:function(response, textStatus, jqXHR) {
						//console.log('response:'+response);
						location.reload();
					},
					error: function(jqXHR, textStatus, errorThrown){
						console.log(textStatus, errorThrown);
					}
				});
			}
		});
		
		function start_game(){
			window.location.replace("./display.php");
		}
			
		function addToList(id, arr){
    		
    		var list = $("#"+id);
			var parent = list.parent();

			list.detach().empty().each(function(i){
				for (var x = 0; x < arr.length; x++){
					$(this).append('<li>' + arr[x] + '</li>');
					if (x == arr.length - 1){
						$(this).appendTo(parent);
					}
				}
			});
    	}
    	
    	function show_server(server_name){
    		$('#player_list').modal('toggle');
    		$('#server-name').html(server_name);
    		updateList();
    	}
    	
    	<?php if(isset($server_id, $server_name)){ ?>
		
		function updateList(){
			$.ajax({
				type: 'POST',
				url: 'process.php?request=update_list',
				cache: false,
				dataType: 'json',
				success:function(response, textStatus, jqXHR) {
					//console.log(response);
					if(response.is_running == true)
						start_game();
					else
						addToList('list', response);
				},
				error: function(jqXHR, textStatus, errorThrown){
					console.log(textStatus, errorThrown);
				}
			});
		}
		
		setInterval(function(){ updateList() }, 200);
		
		
		<?php } ?> 
		$('#leave').click(function (){
			window.location.href = "./leave.php";	
    	});
    	
    	$('#start-game').click(function(){
    		$.ajax({
				type: 'POST',
				url: 'process.php?request=start',
				cache: false,
				dataType: 'json',
				success:function(response, textStatus, jqXHR) {
					//console.log(response);
					if(response == true)
						//redirect to display.php and start the game
						start_game();
					else
						alert('Please try again after sometime.');
				},
				error: function(jqXHR, textStatus, errorThrown){
					console.log(textStatus, errorThrown);
				}
			});
    	});
		
		$('#create-server').click(function (){
    		var name = $('#server_name').val();

			if(name.length < 4 || !name.match(/^[a-zA-Z0-9]+$/))
				$('#response').html('Enter a valid name');
			else{
				$.ajax({
					type: 'POST',
					url: 'process.php?request=register_server',
					data: {
						servername : name,
					},
					cache: false,
					dataType: 'text',
					success:function(response, textStatus, jqXHR) {
						console.log('response:'+response);
						if(response == true){
							$('#response').html('Playground created.');
							location.reload();
						}else
							$('#response').html('Try another playground name.');
					},
					error: function(jqXHR, textStatus, errorThrown){
						console.log(textStatus, errorThrown);
					}
				});
			}
		});
    </script>
</html>
