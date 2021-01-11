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
			<body id="body_profile">
				<?php 
					if(isset($login) && $login){
						?>
						<h3>Nombre de parties jouées:</h3><p>
						<?php 
							echo $db->num_rows;
						?></p>
						<h3>Mes 3 dernières parties:</h3>
							<table id="profile_table">
								<tr id="col_names">
									<td>Difficulté</td>
									<td>Coups</td>
									<td>Secondes</td>
								</tr>
								<?php
								for($i=0;($i<3 && $i<$db->num_rows);$i++){
									$result[]=$db->fetch_assoc();
									?><tr>
										<td>
											<?php echo $result[$i]['difficulty'];?>
										</td>
										<td>
											<?php echo $result[$i]['moves'];?>
										</td>
										<td>
											<?php echo $result[$i]['played'];?>
										</td>
									</tr>
									<?php	
								}
						?></table>
						<h3>Mon score moyen:</h3><p>
						<?php
							$score=($conn->query("SELECT score FROM `users` WHERE login='$login'"))->fetch_assoc();
							echo $score['score'];
						?></p>
						<h3>Ma vitesse moyenne</h3><p>
						<?php 
							$db=$conn->query("SELECT played,moves FROM `games` WHERE login='$login'");
							$speed=0;
							for($i=1;$i<($db->num_rows +1);$i++){
								$result=$db->fetch_assoc();
								$speed=$speed+($result['moves']/$result['played']);
							}
							echo round($speed/$i,3) . " coups par seconde<br>";
						?></p><?php
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