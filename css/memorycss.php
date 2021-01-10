<?php
	header("Content-type: text/css; charset: UTF-8");
?>

<style>

*{cursor:url('../assets/cursor1.png'), auto;}

body{
	cursor:url('../assets/cursor1.png'), auto;
	background-image:url(../assets/bg2.jpg);
	background-size:100%;
	position:relative;
	display:flex;
	flex-direction:column;
	text-align:center;
	justify-content: center;
	align-items:center;
	color:white;
	margin:0;
	width:100%;
}

form{
	margin:0;
}

.form_menu{
	width:100%;
}

#main_area{
	display:flex;
	flex-direction:column;
	text-align:center;
	align-items:center;
	width:100%;
}

#game_area{
	display:flex;
	width:50%;
	min-width:400px;
	flex-wrap:wrap;
	justify-content: center;
}

#game_area form{
	display:flex;
	flex-grow:1;
	width:100px;
	height:155px;
}

#game_area form input{
	flex-grow:1;
	width:100%;
}

.menu_submit{
	width:15%;
}

input[type=submit]:hover{
	cursor:url('../assets/cursor2.png'), pointer;
}

input[id='?']:hover{
	cursor:url('../assets/cursor2.png'), pointer;
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

input[type=text], input[type=password]{
	width:15%;
	padding:0;
}

h1,h2,h3, p{
	font-family:Impact;
	color:#ACD8DF;
	text-shadow:0 1px black, 1px 0 black, -1px 0 black, 0 -1px black;
}

footer{
	width:100%;
}

</style>