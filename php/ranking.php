<?php	
	require_once 'functions.php';
	$_SESSION['conn']=connect_to('memorydb', 'users');
	$users=$_SESSION['conn']->query("SELECT score,login FROM `users` ORDER BY score DESC ");
?>

<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
	<title>Memory</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="UTF-8">
	<link rel="stylesheet" type='text/css' href="../css/memorycss.php?v=<?php echo time(); ?>">
	<link rel="icon" href="../assets/back.png" />
	<script src="https://kit.fontawesome.com/9ddb75d515.js" crossorigin="anonymous"></script>
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Yusei+Magic&display=swap" rel="stylesheet"> 
</head>

<body>
	<header>
		<h1 id="rank_title">Classement</h1>
	</header>
	<main>
		<section id="ranking">
			<?php
				for($i=0;$i<mysqli_num_rows($users);$i++){
					$result[]=$users->fetch_assoc();
				}?>
				<table id="rank_table">
					<?php
					for($j=1;(isset($result[$j]['score']) && $j<15);$j++){
						?><tr>
							<td class="td_num td_num<?php echo $j;?>"><h2 class="rank_num"><?php echo $j;?></h2></td>
							<td class="td_login"><h2 class="rank_login"><?php echo $result[$j]['login'];?></h2></td>
							<td class="td_score td_score<?php echo $j;?>"><h2 class="rank_score"><?php echo $result[$j]['score'];?></h2></td>
						</tr>
					<?php
					}?>
				</table>
		</section>
	</main>

	<footer>
		<a href="../index.php" id="acc_link">Accueil</a>
	</footer>
</body>

</html>