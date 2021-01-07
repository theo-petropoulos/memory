<?php

	require_once 'php/card.php';
	require_once 'php/functions.php';

	$page = $_SERVER['PHP_SELF'];

	if(!isset($_SESSION)){
		session_start();
	}

	//Si l'utilisateur saisit les paramètres de la partie
	if(isset($_POST['level']) && isset($_POST['time'])){
		//On rentre les paramètres de la partie en session
		$_SESSION['time']=$_POST['time'];
		$_SESSION['level']=$_POST['level'];
		//On génère le plateau de jeu en session
		$_SESSION['deck']=generate_cards($_SESSION['level']);
		//On initialise le compteur de coups
		$_SESSION['play_count']=0;
	}

	//Si deux cartes ne sont pas identiques
	if(isset($_SESSION['mismatch']) && $_SESSION['mismatch'] && isset($_SESSION['card1']) && isset($_SESSION['card2'])){
		//On retourne les deux cartes côté verso
		unset_cards($_SESSION['deck'], $_SESSION['card1'], $_SESSION['card2'], $_SESSION['mismatch']);
	}

	//Si l'utilisateur choisit une carte
	if(isset($_POST['check_card']) && $_POST['check_card']){
		//On joue un coup
		play_turn(
			$_SESSION['play_count'], $_SESSION['deck'], $_POST['card_value'], 
			$_SESSION['card1'], $_SESSION['card2'], $_SESSION['mismatch'], 
			$_SESSION['level'], $_SESSION['time'], $page);
		//On retire les données envoyées par l'utilisateur
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

			//Si aucun choix de menu n'a été fait & si on est pas dans une partie ( ingame )
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

				//Sinon si on a fait le choix numéro 1 ( se connecter ) & si on est pas dans une partie
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

				//Sinon si on a fait le choix numéro 2 ( jouer en invité ) & si on est pas dans une partie
				else if(isset($_POST['menu2']) && $_POST['menu2'] && !isset($_SESSION['ingame'])){
					//On initialise la variable ingame à 1
					$_SESSION['ingame']=1;
				}

				//Si on est dans une partie ( ingame = 1)
				if(isset($_SESSION['ingame']) && $_SESSION['ingame']){
					//Si le plateau a bien été généré
					if(isset($_SESSION['deck']) && $_SESSION['deck']){
						?>
						<!--On définit la zone de jeu -->
						<section id="game_area">
							<?php
							//On parcourt les cartes du plateau tant qu'elles existent
							for($i=0;isset($_SESSION['deck'][$i]);$i++){
								//On créé une div pour chaque carte existante
								?><div class="card" id="div_card<?php echo $_SESSION['deck'][$i]->get_value();?>">
									<form action="index.php" method="post">
										<input type="checkbox" name="check_card" checked hidden>
										<!--La valeur de la carte récupérée dans la classe-->
										<input type="checkbox" 
										name="card_value" 
										value="<?php echo intval($_SESSION['deck'][$i]->get_value())?>" checked hidden>
										<?php 
										//Si la carte est face verso
										if($_SESSION['deck'][$i]->get_state()=='verso'){
											?>
											<!--Alors son id vaut '?'-->
											<input type="submit" id="?" value="">
											<?php
										}
										//Sinon si la carte est face recto
										else if($_SESSION['deck'][$i]->get_state()=='recto'){
											?>
											<!--Alors son id vaut la valeur de la carte récupérée dans la classe-->
											<input type="submit" id="<?php echo intval($_SESSION['deck'][$i]->get_value())?>" value="" disabled>
											<style>
												<?php 
												//Pour afficher les bonnes images sur les cartes, on cherche à savoir si sa valeur
												//est inférieure ou égale au nombre de paires rentrées par le joueur
												//Exemple, les cartes de 1 à 8 pour un jeu à 8 paires
												//--
												//Ensuite, si la valeur est inférieure au nombre de paire, on attribue l'image 
												//correspondante à la carte, les assets étant numérotés dans cet ordre
												//--
												//Sinon si la valeur est supérieure au nombre de paire, on déduit le nombre de paires
												//à la valeur de la carte pour obtenir la même image que son clone
												//Exemple : Sur un jeu à 8 paires, la carte 5 aura un design de chat. 
												//La carte 13 aura également un design de chat (13-8=5)
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

					//Si le plateau n'a pas encore été généré
					else{
						?>
						<!--On demande à l'utilisateur les paramètres de la partie-->
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
			<!--Si le compteur de coup a été initalité-->
			<?php if(isset($_SESSION['play_count'])){
				?><p><?php echo "Compteur de coups : " . $_SESSION['play_count'] . "<br>";?></p><?php
			}
			?>
		</footer>

	</body>
</html>