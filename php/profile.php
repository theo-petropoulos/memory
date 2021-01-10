<?php
	require_once 'functions.php';
	$conn=connect_to('memorydb', '');
	if(isset($_GET['menu6']) && $_GET['menu6']){
		if(look_for($_GET['menu6'], $conn)){
			$login=$_GET['menu6'];
			$db=$conn->query("SELECT * FROM `games` WHERE login='$login' ORDER BY id DESC");
		}
		else{
			die("Vous ne pouvez pas accéder à ce contenu.");
		}
	}
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
			<h1 id="profile_title">Profil</h1>
			<h2><?php if(isset($login) && $login){echo $login;}?></h2>
		</header>

		<main>
			<body>
				<?php 
					if(isset($login) && $login){
						?>
						<p>Nombre de parties jouées</p>
						<?php 
							echo $db->num_rows;
						?>
						<p>Mes 3 dernières parties</p>
						<?php
							for($i=0;($i<3 && $i<$db->num_rows);$i++){
								$result[]=$db->fetch_assoc();
							}
							var_dump($result);
						?>
						<p>Mon score moyen</p>
						<?php
							$score=($conn->query("SELECT score FROM `users` WHERE login='$login'"))->fetch_assoc();
							echo $score['score'];
						?>
						<p>Ma vitesse moyenne</p>
						<?php 
							$db=$conn->query("SELECT played,moves FROM `games` WHERE login='$login'");
							$speed=0;
							for($i=1;$i<($db->num_rows +1);$i++){
								$result=$db->fetch_assoc();
								$speed=$speed+($result['moves']/$result['played']);
							}
							echo round($speed/$i,3) . " coups par seconde<br>";
					}
					else{
						die("Vous ne pouvez pas afficher ce contenu.");
					}?>
					<br>
					<a href="../index.php" id="acc_link">Accueil</a>
			</body>
		</main>

	</body>

</html>