<?php

	require_once 'php/class.php';
	require_once 'php/functions.php';

	$page = $_SERVER['PHP_SELF'];
	
	//Créé la connexion avec la base de donnée, la créée si besoin, et créée la table users dans cette base de donnée
	$conn=connect_to('memorydb');

	if(!isset($_SESSION)){
		session_start();
	}

	//Si l'utilisateur essaie de s'inscrire
	if(isset($_POST['login']) && $_POST['login'] && isset($_POST['password']) 
		&& $_POST['password'] && isset($_POST['vpassword']) && $_POST['vpassword']){
		$user=new user($_POST['login'], $_POST['password'], $_POST['vpassword']);
		$user->create_user();
	}

	//Si l'utilisateur veut revenir à l'accueil, on unset tout sauf l'identification, s'il est en jeu, il peut choisir d'annuler
	if(isset($_POST['yaccueil']) && $_POST['yaccueil']==1){
		if(isset($_SESSION['connected'])){$temp_conn=$_SESSION['connected'];}
		unset($_POST);
		reset_accueil(
				$_SESSION['time'], $_SESSION['deck'], $_SESSION['ingame'],
				$_SESSION['level'], $_SESSION['play_count'], $_SESSION['card1'],
				$_SESSION['card2'], $_SESSION['memory'], $_SESSION['match_found']);
		if(isset($temp_conn) && $temp_conn){$_SESSION['connected']=$temp_conn;$temp_conn=NULL;}
	}

	else if(isset($_POST['naccueil']) && $_POST['naccueil']==1){
		unset($_POST);
	}

	//Si l'utilisateur saisit les paramètres de la partie
	if(isset($_POST['level']) && isset($_POST['time'])){
		if(in_array($_POST['level'], range(3,12)) && in_array($_POST['time'], range(1,5))){
			//On rentre les paramètres de la partie en session
			$_SESSION['time']=$_POST['time'];
			$_SESSION['level']=$_POST['level'];
			//On génère le plateau de jeu en session
			$_SESSION['deck']=generate_cards($_SESSION['level']);
			//On initialise le compteur de coups
			$_SESSION['play_count']=0;
		}
		else{
			die("Les valeurs saisies sont incorrectes.");
		}
	}

	//Si l'utilisateur confirme la réinitialisation
	if(isset($_POST['yreset']) && $_POST['yreset']==1){
		reset_game(
				$_SESSION['time'], $_SESSION['deck'], $_SESSION['ingame'],
				$_SESSION['level'], $_SESSION['play_count'], $_SESSION['card1'],
				$_SESSION['card2'], $_SESSION['memory'], $_SESSION['match_found']);
		unset($_POST);
	}

	//Si l'utilisateur annule la réinitialisation
	else if(isset($_POST['nreset']) && $_POST['nreset']==1){
		unset($_POST['reset']);
		unset($_POST['nreset']);
	}

	//Si deux cartes ne sont pas identiques
	if(isset($_SESSION['mismatch']) && $_SESSION['mismatch']==1 && isset($_SESSION['card1']) && isset($_SESSION['card2']) 
	&& in_array($_SESSION['card1'], range(1,12)) && in_array($_SESSION['card2'], range(1,12))){
		//On retourne les deux cartes côté verso
		unset_cards($_SESSION['deck'], $_SESSION['card1'], $_SESSION['card2'], $_SESSION['mismatch']);
	}

	//Si l'utilisateur choisit une carte
	if(isset($_POST['card_value']) && in_array($_POST['card_value'], range(1,12))){
		//On joue un coup
		play_turn(
			$_SESSION['play_count'], $_SESSION['deck'], $_POST['card_value'], 
			$_SESSION['card1'], $_SESSION['card2'], $_SESSION['mismatch'], 
			$_SESSION['level'], $_SESSION['time'], $page);
		//On retire les données envoyées par l'utilisateur
		unset($_POST);
	}

	if(isset($_POST['aiplay']) && $_POST['aiplay']==1){
		//Tant que la condition de fin de partie est nulle
		while(!verify_game($_SESSION['deck'])){
			$i=$j=1;
			//Tant qu'il existe une carte $i en mémoire et qu'aucune paire n'a été trouvée
			if(isset($_SESSION['memory'][$i]) && $_SESSION['memory']){
				while(isset($_SESSION['memory'][$i])){
					//On parcourt une deuxième fois la mémoire pour trouver une paire
					while(isset($_SESSION['memory'][$j])){
						//Si une paire est trouvée, on initialise la variable match_found pour sortir de la boucle
						//On met en mémoire temporaire les 2 cartes de la paire
						if( ($_SESSION['memory'][$i]+$_SESSION['level'])==$_SESSION['memory'][$j] ||
							($_SESSION['memory'][$j]+$_SESSION['level'])==$_SESSION['memory'][$j]) {
							$_SESSION['match_found']=1;
							$card_1=$i;
							$card_2=$j;
						}
						$j++;
					}
					$i++;
				}
				//Si une paire est trouvée et que la première carte n'est pas encore en mémoire
				if(isset($_SESSION['match_found']) && $_SESSION['match_found']==1 && !isset($_SESSION['card1'])){
					$card=$card_1;
				}
				//Sinon si une paire est trouvée et que la première carte est en mémoire et que la deuxième carte n'est pas en mémoire
				else if(isset($_SESSION['match_found']) && $_SESSION['match_found']==1 && 
						isset($_SESSION['card1']) && $_SESSION['card1'] && !isset($_SESSION['card2'])){
					$card=$card_2;
					unset($_SESSION['match_found']);
				}
			}

			//Si aucune paire n'a été trouvée, on joue une carte qui n'a pas encore été jouée
			if(!isset($_SESSION['match_found'])){
				for($i=1;isset($_SESSION['memory'][$i]);$i++){
				}
				$card=$i;
				$_SESSION['memory'][$i]=$card;
			}

			//On joue un tour avec la variable $card définie plus haut
			play_turn(
				$_SESSION['play_count'], $_SESSION['deck'], $card, 
				$_SESSION['card1'], $_SESSION['card2'], $_SESSION['mismatch'], 
				$_SESSION['level'], $_SESSION['time'], $page);
			
			//On vide la mémoire card1 et card2 session toutes les 2 cartes jouées
			if(isset($_SESSION['card1']) && isset($_SESSION['card2'])){unset($_SESSION['card1'],$_SESSION['card2']);}
		}
	}
	?>

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
				(!isset($_POST['menu4']) || !$_POST['menu4']) &&
				(!isset($_POST['menu5']) || !$_POST['menu5']) &&
				!isset($_SESSION['ingame'])){
					if(
						!isset($_SESSION['connected']) && !isset($_POST['menu1']) && 
						!isset($_POST['menu2']) && !isset($_POST['menu3']) && 
						!isset($_POST['menu4']) && !isset($_POST['menu5'])){?>
						<div id="connect">
							<form method="post" action="index.php">
								<input type="checkbox" name="menu1" checked hidden>
								<input type="submit" value="Se connecter">
							</form>
						</div>
						<div id="register">
							<form method="post" action="index.php">
								<input type="checkbox" name="menu4" checked hidden>
								<input type="submit" value="S'inscrire">
							</form>
						</div>
					<?php }

					else if(isset($_SESSION['connected']) && $_SESSION['connected']){?>
						<div id="logged">
							<form method="post" action="index.php">
								<input type="checkbox" name="menu5" checked hidden>
								<input type="submit" value="Jouer">
							</form>
						</div>
					<?php } ?>

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
							<br>
							<input type="submit" value="Connexion">
						</form>
					</div>
					<?php
				}

				//Sinon si on a fait le choix numéro 2 ( jouer en invité ) & si on est pas dans une partie
				else if(isset($_POST['menu2']) && $_POST['menu2'] && !isset($_SESSION['ingame'])){
					//On initialise la variable ingame à 1
					$_SESSION['ingame']=1;
				}

				else if(isset($_POST['menu4']) && $_POST['menu4'] && !isset($_SESSION['ingame'])){
					?>
					<form method="post" action="index.php">
						<label for="login">Login :<br></label>
						<input type="text" name="login" placeholder="Ex: John-Doe64" required>
						<br>
						<label for="password">Mot de passe :<br></label>
						<input type="password" name="password" required>
						<br>
						<label for="vpassword">Confirmer le mot de passe :<br></label>
						<input type="password" name="vpassword" required>
						<br>
						<input type="submit" value="Envoyer">
					</form>
					<?php
				}

				//Si on est dans une partie ( ingame = 1)
				if(isset($_SESSION['ingame']) && $_SESSION['ingame']==1){
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
						if(isset($_SESSION['ingame']) && $_SESSION['ingame']){
							?><form method="post" action="index.php">
								<input type="hidden" name="reset">
								<input type="submit" value="Relancer">
							</form>
							<form method="post" action="index.php">
								<input type="hidden" name="aiplay" value="1">
								<input type="submit" value="AI Play">
							</form>
							<?php
						}
						if(isset($_POST['reset'])){
							?><p>Êtes-vous sûr de vouloir réinitialiser le niveau ?</p>
							<form method="post" action="index.php">
								<input type="hidden" name="yreset" value=1>
								<input type="submit" value="Oui">
							</form>
							<form method="post" action="index.php">
								<input type="hidden" name="nreset" value=1>
								<input type="submit" value="Non">
							</form>
						<?php
						}
					}

					//Si le plateau n'a pas encore été généré
					else{
						?>
						<!--On demande à l'utilisateur les paramètres de la partie-->
						<form method="post" action="index.php">
							<label for="level">Nombre de paires (de 3 à 12):<br></label>
							<input type="number" name="level" min=3 max=12>
							<br>
							<label for="time">Temps d'affichage (en secondes) :<br></label>
							<input type="number" name="time" min=1 max=5>
							<input type="checkbox" checked hidden name="generate">
							<br>
							<input type="submit" value="Générer le niveau">
						</form>
						<?php
					}
				}?>

		</main>

		<footer>
			<form method="post" action="index.php">
				<input type="hidden" name="accueil" value="1">
				<input type="submit" value="Accueil">
			</form>
			<?php 
			if(isset($_POST['accueil']) && $_POST['accueil'] && isset($_SESSION['ingame']) && $_SESSION['ingame']){
				?>
				<p>La partie en cours sera annulée, continuer ?</p>
				<form method="post" action="index.php">
					<input type="hidden" name="yaccueil" value=1>
					<input type="submit" value="Oui">
				</form>
				<form method="post" action="index.php">
					<input type="hidden" name="naccueil" value=1>
					<input type="submit" value="Non">
				</form>	
			<?php
			}
			//Si le compteur de coup a été initalité
			if(isset($_SESSION['play_count']) && $_SESSION['play_count']){
				?><p><?php echo "Compteur de coups : " . $_SESSION['play_count'] . "<br>";?></p><?php
			}
			?>
		</footer>

	</body>
</html>

<?php
	$conn->close();
?>