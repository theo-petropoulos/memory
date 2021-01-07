<?php

	require_once 'php/card.php';
	require_once 'php/functions.php';

	$page = $_SERVER['PHP_SELF'];

	if(!isset($_SESSION)){
		session_start();
	}

	if(isset($_POST['level']) && isset($_POST['time'])){
		$_SESSION['time']=$_POST['time'];
		$_SESSION['level']=$_POST['level'];
		$_SESSION['deck']=generate_cards($_SESSION['level']);
		$_SESSION['play_count']=0;
	}

	if(isset($_SESSION['mismatch']) && $_SESSION['mismatch'] && isset($_SESSION['card1']) && isset($_SESSION['card2'])){
		unset_cards($_SESSION['deck'], $_SESSION['card1'], $_SESSION['card2'], $_SESSION['mismatch']);
	}

	if(isset($_POST['check_card']) && $_POST['check_card']){
		play_turn(
			$_SESSION['play_count'], $_SESSION['deck'], $_POST['card_value'], 
			$_SESSION['card1'], $_SESSION['card2'], $_SESSION['mismatch'], 
			$_SESSION['level'], $_SESSION['time'], $page);
		unset($_POST);
	}?>

	<!DOCTYPE html>

	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

	<head>
		<title>Memory</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta charset="UTF-8">
		<link rel="stylesheet" type='text/css' href="css/memorycss.php?v=<?php echo time(); ?>">
		<script src="https://kit.fontawesome.com/9ddb75d515.js" crossorigin="anonymous"></script>
		<link href="https://fonts.googleapis.com/css2?family=Syne&display=swap" rel="stylesheet"> 
	</head>

	<body>

		<header>
			<h1>Memory</h1>
			<h2>Card Matching Game</h2>
			<p>test blablabla</p>
		</header>

		<main id="main_area">

			<?php

			if( (!isset($_POST['menu1']) || !$_POST['menu1']) &&
				(!isset($_POST['menu2']) || !$_POST['menu2']) &&
				(!isset($_POST['menu3']) || !$_POST['menu3']) &&
				(!isset($_SESSION['ingame']))){?>
					<div id="connect">
						<form method="post" action="index.php">
							<input type="checkbox" name="menu1" checked hidden>
							<input type="submit" value="Se Connecter">
						</form>
					</div>

					<div id="guest">
						<form method="post" action="index.php">
							<input type="checkbox" name="menu2" checked hidden>
							<input type="submit" value="Jouer en invité">
						</form>
					</div>

					<div id="ranking">
						<form method="post" action="ranking.php">
							<input type="checkbox" name="menu3" checked hidden>
							<input type="submit" value="Classement">
						</form>
					</div>
					<?php
				}

				else if(isset($_POST['menu1']) && $_POST['menu1'] && !isset($_SESSION['ingame'])){
					?>
					<div id="connect_form">
						<form method="post" action="index.php">
							<label for="login">Login :<br></label>
							<input type="text" name="login" required>
							<label for="password"><br>Mot de passe:<br></label>
							<input type="password" name="password" required>
							<input type="submit" value="Connexion">
						</form>
						<a href="index.php">Retour</a>
					</div>
					<?php
				}

				else if(isset($_POST['menu2']) && $_POST['menu2'] && !isset($_SESSION['ingame'])){
					$_SESSION['ingame']=1;
				}

				if(isset($_SESSION['ingame']) && $_SESSION['ingame']){
					if(isset($_SESSION['deck']) && $_SESSION['deck']){
						?>
						<section id="game_area">
							<?php
							for($i=0;isset($_SESSION['deck'][$i]);$i++){
								?><div class="card" id="div_card<?php echo $_SESSION['deck'][$i]->get_value();?>">
									<form action="index.php" method="post">
										<input type="checkbox" name="check_card" checked hidden>
										<input type="checkbox" 
										name="card_value" 
										value="<?php echo intval($_SESSION['deck'][$i]->get_value())?>" checked hidden>
										<?php 
										if($_SESSION['deck'][$i]->get_state()=='verso'){
											?>
											<input type="submit" id="?" value="">
											<?php
										}
										else if($_SESSION['deck'][$i]->get_state()=='recto'){
											?>
											<input type="submit" id="<?php echo intval($_SESSION['deck'][$i]->get_value())?>" value="" disabled>
											<style>
												<?php 
												if(intval($_SESSION['deck'][$i]->get_value())<=$_SESSION['level']){
													?>
													input[id="<?php echo intval($_SESSION['deck'][$i]->get_value())?>"]{
														background:url('assets/card<?php echo intval($_SESSION['deck'][$i]->get_value())?>');
														background-size:100%;
													}
													<?php
												}
												else if(intval($_SESSION['deck'][$i]->get_value())>$_SESSION['level']){
													$temp_val=intval($_SESSION['deck'][$i]->get_value())-$_SESSION['level'];
													?>
													input[id="<?php echo intval($_SESSION['deck'][$i]->get_value())?>"]{
														background:url('assets/card<?php echo $temp_val?>');
														background-size:100%;
													}<?php
												}
												?>
											</style>
											<?php
										}
										?>
									</form>
								</div>
								<?php
							}
							?>
						</section>
						<?php
					}

					else{
						?>
						<form method="post" action="index.php">
							<label for="level">Nombre de paires (de 3 à 12):</label>
							<input type="number" name="level" min=3 max=12>
							<label for="time">Temps d'affichage (en secondes) :</label>
							<input type="number" name="time" min=1 max=5>
							<input type="checkbox" checked hidden name="generate">
							<input type="submit" value="Générer le niveau">
						</form>
						<?php
					}
				}?>

		</main>

		<footer>
			<?php if(isset($_SESSION['play_count'])){
				?><p><?php echo "Compteur de coups : " . $_SESSION['play_count'] . "<br>";?></p><?php
			}
			?>
		</footer>

	</body>
</html>