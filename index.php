<html>
    <head>
        <link rel="stylesheet" href="./css/main.css" type="text/css">
        <link rel='stylesheet'  href='http://192.168.0.100/libraries/css/bootstrap.min.css'>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<script src="jquery.min.js"></script>
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
           	}
           	
           	.container{
           		max-width:680px;
           	}
           	  	
           	#response{
               font-family: 'Raleway', sans-serif;
               text-align: center;
               font-size: 20px;
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
		            <div class="input-group" id='username-holder'>
		            	<input type="text" class="form-control" id="user_id" placeholder="Enter your name ">
				        <span class="input-group-btn">
				        	<button class="btn btn-primary" id="submit">Create ID</button>
				        </span>
		            </div>
                                
                    <div class="input-group" id='server-holder'>
		                <input type="text" class="form-control" id="server_name" placeholder="Enter server name">
				            <span class="input-group-btn">
				            	<button class="btn btn-primary" id="create-server">Create server</button>	
				            </span>
		                <p><h2>OR</h2></p>
		                <button class="btn btn-primary" id="join-server">Join server</button>
                    </div>
                </div>
       		</div>
		   <div id="response"></div>
		   </div>
       </center>
    </body>
    <script>
    
    	function submit_name(name){
    		return true;
    	}
    
    	$(document).ready(function(){
    		$('#server-holder').hide();
    	});
    
    	$('#submit').click(function (){
    		var name = $('#user_id').val();

			if(name.length < 4 || !name.match(/^[a-zA-Z0-9]+$/))
				$('#response').html('Enter a valid name');
			else
				if(submit_name(name)){
					$('#username-holder').hide();
					$('#server-holder').show();
				}else
					$('#response').html('Try another username.');
		});
		
		$('#join-server').click(function (){
		
			
		});
		
		
		$('#create-server').click(function (){
    		var name = $('#server_name').val();

			if(name.length < 4 || !name.match(/^[a-zA-Z0-9]+$/))
				$('#response').html('Enter a valid name');
			else{
			
			}
		});
    </script>
</html>
