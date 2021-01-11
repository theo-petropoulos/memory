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

#menu_area{
	width:100%;
}

#game_area{
	display:flex;
	width:60%;
	min-width:500px;
	flex-wrap:wrap;
	justify-content: center;
	margin-bottom:1%;
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
	min-width:180px;
	background-color:#569c90;
	color:#E7D6C1;
	border-radius:20px;
	padding:0.5% 0 0.5% 0;
	margin-bottom:0.5%;
	border:5px 0 white;
	font-family:'Yusei Magic', Courier;
}

.menu_submit:hover{
	background-color:#6EC7B8;
	color:white;
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

input[type=text], input[type=password], input[type="number"]{
	width:15%;
	min-width:180px;
	height:30px;
	padding:0;
	margin-bottom:1%;
	background:#AA9B90;
}

label{
	font-family:'Yusei Magic', Courier;
	font-size:1.5em;
	color:#E2CBAA;
	text-shadow:0 1px black, 1px 0 black, -1px 0 black, 0 -1px black;
}

h1,h2,h3, p{
	font-family:'Yusei Magic', Courier;
	color:white;
	text-shadow:0 1px black, 1px 0 black, -1px 0 black, 0 -1px black;
}

h1{
	font-size:3em;
	letter-spacing:0.2em;
	margin:1% 0 1% 0;
}

h2{
	font-size:2em;
	letter-spacing:0.2em;
	margin:1% 0 2% 0;
}

footer{
	width:100%;
}

/** RANKING **/

#rank_title{
	text-decoration:underline;
}

#rank_table{
	margin-bottom:2%;
}

#rank_table td{
	border:3px solid black;
	border-collapse:collapse;
	text-align:center;
}

.td_num{
	background:#569C90;
}

.td_login, .td_score{
	background:#E7D6B6;
}

.td_num1{
	border-top-left-radius: 10px;
	background:#FFD800;
}

#acc_link{
	font-family:'Yusei Magic', Courier;
	color:white;
	font-size:1.5em;
	text-decoration:none;
	background:grey;
	padding:0.2% 2% 0.2% 2%;
	border-radius:10px;
	border:1px solid white;
}

#acc_link:hover{
	background:#D5D5D5;
}

/**PROFILE**/

#body_profile{
}

#body_profile main{
	width:50%;
	min-width:300px;
}

#body_profile h3{
	background:#569C90;
	color:#E7D0B0;
	padding:0.5% 3% 0.5% 3%;
	border:2px solid #E7D0B0;
}

#body_profile table{
	border-collapse:collapse;
	text-align:center;
	margin-left:auto;
	margin-right:auto;
}

#body_profile td{
	border:3px solid black;
	font-family:'Yusei Magic';
	text-shadow:1px 0 black, 0 1px black, -1px 0 black, 0 -1px black;
	background:#E7D6B6;
}

#col_names td{
	background:#AE9870;
	padding:10px;
}

</style>