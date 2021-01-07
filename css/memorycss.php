<?php
	header("Content-type: text/css; charset: UTF-8");
	require_once '../index.php';
?>

<style>

html{
}

body{
	background-image:radial-gradient(circle, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.5) 30%, rgba(177,181,189,0) 80%), url(../assets/bg2.png);
	background-size:100%;
	position:relative;
	display:flex;
	flex-direction:column;
	text-align:center;
	justify-content: center;
	align-items:center;
	color:white;
}

#game_area{
	display:flex;
	width:70%;
	flex-wrap:wrap;
}

#game_area form{
	display:flex;
	width:130px;
	height:200px;
}

#game_area form input{
	flex-grow:1;
	width:100%;
}

input[id='?']{
	background:url('../assets/back.png');
	background-size:100%;
}

h1,h2,h3, p{
	font-family:Courier;
	color:#ACD8DF;
	text-shadow:3px 0 black, 0 3px black;
}

</style>