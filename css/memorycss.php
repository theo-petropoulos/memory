<?php
	header("Content-type: text/css; charset: UTF-8");
	require_once '../index.php';
?>

<style>

html{
}

body{
	background-image:url(../assets/bg2.jpg);
	background-size:100%;
	position:relative;
	display:flex;
	flex-direction:column;
	text-align:center;
	justify-content: center;
	align-items:center;
	color:white;
}

#main_area{
	display:flex;
	flex-direction:column;
	text-align:center;
	align-items:center;
}

#game_area{
	display:flex;
	width:60%;
	min-width:400px;
	flex-wrap:wrap;
	justify-content: center;
}

#game_area form{
	display:flex;
	flex-grow:1;
	width:100px;
	height:155px;
	margin:0;
}

#game_area form input{
	flex-grow:1;
	width:100%;
}

input[id='?']:hover{
	cursor:pointer;
	box-shadow: inset 0 0 0 1000px rgba(255,255,255,.3);
	border:3px solid rgba(255,255,255,0.2);
	border-radius:5px;
}

input[id='?']{
	background:url('../assets/back.png');
	background-size:100%;
	border:3px solid rgba(0,0,0,0.6);
	border-radius:5px;
}

h1,h2,h3, p{
	font-family:Courier;
	color:#ACD8DF;
	text-shadow:3px 0 black, 0 3px black;
}

</style>