<?php


	require_once('MySQL.php');
	
	$db = new MySQL('localhost', 'root', '2529@mysql');

    $db->db_connect('bingo');
    
    session_start();
