<?php
	
	require_once 'php/class.php';
	require_once 'php/functions.php';

	$page = $_SERVER['PHP_SELF'];
	
	if(!isset($_SESSION)){
		session_start();
	}

	//Se connecte à la base de donnée, la créée si besoin, et créée la table users si elle n'existe pas
	$_SESSION['conn']=connect_to('memorydb', 'users');
	//Identique, créé la table games si elle n'existe pas
	$_SESSION['conn2']=connect_to2('memorydb', 'games');

	//Si l'utilisateur essaie de s'inscrire
	if(isset($_POST['login']) && $_POST['login'] && isset($_POST['password']) 
		&& $_POST['password'] && isset($_POST['vpassword']) && $_POST['vpassword']){
		$_SESSION['user']=new User($_POST['login'], $_POST['password'], $_POST['vpassword']);
		$_SESSION['user']->createUser();
		unset($_SESSION['user']);
	}

	//Si l'utilisateur essaie de se connecter
	if(isset($_POST['clogin']) && $_POST['clogin'] && isset($_POST['cpassword']) && $_POST['cpassword']){
		$_SESSION['user']=new User($_POST['clogin'], $_POST['cpassword'], NULL);
		$_SESSION['user']->logUser($_SESSION['connected']);
		if(isset($_SESSION['connected']) && $_SESSION['connected']=='success'){
			$_SESSION['login']=$_SESSION['user']->getLogin();
		}
	}

	//Si l'utilisateur veut se déconnecter
	if(isset($_POST['disconnect']) && $_POST['disconnect']==1){
		unset($_SESSION['connected'], $_SESSION['user'], $_SESSION['login']);
	}

	//Si l'utilisateur veut revenir à l'accueil, on unset tout sauf l'identification, s'il est en jeu, il peut choisir d'annuler
	if(isset($_POST['yaccueil']) && $_POST['yaccueil']==1){
		unset($_POST);
		//Si l'utilisateur était connecté, on met en mémoire son login, son "pass" de connexion, et la connexion à la db
		if(isset($_SESSION['connected']) && isset($_SESSION['login']) && $_SESSION['connected']='success' && $_SESSION['login']){
			$temp_login=$_SESSION['login'];
			$temp_connect=$_SESSION['connected'];
			$temp_user=$_SESSION['user'];
		}
		session_unset();
		//Si on a mis en mémoire ces paramètres, on les réinjecte en session
		if(isset($temp_login) && isset($temp_connect)){
			$_SESSION['login']=$temp_login;
			$_SESSION['connected']=$temp_connect;
			$_SESSION['user']=$temp_user;
		}
		$_SESSION['AI_play_count'] = 'R';
		$_SESSION['conn']=connect_to('memorydb', 'users');
		$_SESSION['conn2']=connect_to2('memorydb', 'games');
	}

	//Si l'utilisateur annule le retour à l'accueil
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
			?><p>Erreur : Les valeurs saisies sont incorrectes</p><?php
			return 0;
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
	&& in_array($_SESSION['card1'], range(1,2*$_SESSION['level'])) && in_array($_SESSION['card2'], range(1,2*$_SESSION['level']))){
		//On retourne les deux cartes côté verso
		echo "MISMATCH";
		unset_cards($_SESSION['deck'], $_SESSION['card1'], $_SESSION['card2'], $_SESSION['mismatch']);
	}

	//Si l'utilisateur choisit une carte
	if(isset($_POST['card_value']) && in_array($_POST['card_value'], range(1,2*$_SESSION['level']))){
		//On joue un coup
		play_turn(
			$_SESSION['play_count'], $_SESSION['deck'], $_POST['card_value'], 
			$_SESSION['card1'], $_SESSION['card2'], $_SESSION['mismatch'], 
			$_SESSION['level'], $_SESSION['time'], $page);
		//On créé un timestamp des inputs utilisateur pour son classement, s'il est connecté
		if(isset($_SESSION['connected']) && $_SESSION['connected']=='success' && isset($_SESSION['login']) && $_SESSION['login']){
			$_SESSION['log_time'][]=time();
		}
		//On retire les données envoyées par l'utilisateur
		if(verify_game($_SESSION['deck']) && isset($_SESSION['connected']) && $_SESSION['connected']=='success' 
		&& isset($_SESSION['login']) && $_SESSION['login'] && (!isset($_SESSION['invite']) || !$_SESSION['invite'])){
			$_SESSION['user']->storeGame($_SESSION['level'], $_SESSION['play_count'], $_SESSION['log_time']);
		}
		unset($_POST);
	}

	//Si l'utilisateur souhaite obtenir un score "IA"
	//L'IA joue sa partie, on enregistre son score et on remet le plateau à zero en générant une nouvelle partie
	if(isset($_POST['aiplay']) && $_POST['aiplay']==1){
		while(!verify_game($_SESSION['deck'])){
			ai_play($_SESSION['deck'], $_SESSION['memory'], $_SESSION['level'], $_SESSION['time'],
					$_SESSION['match_found'], $_SESSION['card1'], $_SESSION['card2'],
					$_SESSION['play_count'], $_SESSION['mismatch']);
		}
		for($i=0;isset($_SESSION['deck'][$i]);$i++){
			$_SESSION['deck'][$i]->unselectCard();
		}
		unset($_SESSION['card1'], $_SESSION['card2']);
	}
	?>

	<!DOCTYPE html>

	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

	<head>
		<title>Memory</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta charset="UTF-8">
		<link rel="stylesheet" type='text/css' href="css/memorycss.php?v=<?php echo time(); ?>">
		<link rel="icon" href="assets/back.png" />
		<script src="https://kit.fontawesome.com/9ddb75d515.js" crossorigin="anonymous"></script>
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Yusei+Magic&display=swap" rel="stylesheet"> 
	</head>

	<body>

		<header>
			<h1>Memory</h1>
			<h2>Card Matching Game</h2>
			<?php if(isset($_SESSION['connected']) && $_SESSION['connected']=='success' && isset($_SESSION['login']) && $_SESSION['login']){
				?><h3>Bienvenue <strong><?php echo $_SESSION['login'] ?></strong></h3><?php
			}?>
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
					?>
					<div id="menu_area"><?php
					if(
						!isset($_SESSION['connected']) && !isset($_POST['menu1']) && 
						!isset($_POST['menu2']) && !isset($_POST['menu3']) && 
						!isset($_POST['menu4']) && !isset($_POST['menu5'])){?>
						<div id="connect" class="form_menu">
							<form method="post" action="index.php">
								<input type="checkbox" name="menu1" checked hidden>
								<input class="menu_submit" type="submit" value="Se connecter">
							</form>
						</div>
						<div id="register" class="form_menu">
							<form method="post" action="index.php">
								<input type="checkbox" name="menu4" checked hidden>
								<input class="menu_submit" type="submit" value="S'inscrire">
							</form>
						</div>
					<?php }

					else if(isset($_SESSION['connected']) && $_SESSION['connected']=='success'){?>
						<div id="logged" class="form_menu">
							<form method="post" action="index.php">
								<input type="checkbox" name="menu5" checked hidden>
								<input class="menu_submit" type="submit" value="Jouer">
							</form>
						</div>
					<?php } ?>

					<div id="guest" class="form_menu">
						<form method="post" action="index.php">
							<input type="checkbox" name="menu2" checked hidden>
							<input class="menu_submit" type="submit" value="Jouer en invité">
						</form>
					</div>

					<?php if(isset($_SESSION['connected']) && $_SESSION['connected']=='success'){?>
						<div id="profile" class="form_menu">
							<form method="get" action="php/profile.php">
								<input type="checkbox" name="menu6" value="<?php echo $_SESSION['login'];?>" checked hidden>
								<input class="menu_submit" type="submit" value="Profil">
							</form>
						</div>
					<?php } ?>

					<div id="ranking" class="form_menu">
						<form method="post" action="php/ranking.php">
							<input type="checkbox" name="menu3" checked hidden>
							<input class="menu_submit" type="submit" value="Classement">
						</form>
					</div>
					<?php
				}

				//Sinon si on a fait le choix numéro 1 ( se connecter ) & si on est pas dans une partie
				else if(isset($_POST['menu1']) && $_POST['menu1'] && !isset($_SESSION['ingame'])){
					?>
					<div id="connect_form" class="form_menu">
						<form method="post" action="index.php">
							<label for="clogin">Login:<br></label>
							<input type="text" name="clogin" required>
							<label for="cpassword"><br>Mot de passe:<br></label>
							<input type="password" name="cpassword" required>
							<br>
							<input class="menu_submit" type="submit" value="Connexion">
						</form>
					</div>
					<?php
				}

				//Sinon si on a fait le choix numéro 2 ( jouer en invité ) & si on est pas dans une partie
				else if( ( (isset($_POST['menu2']) && $_POST['menu2']) || (isset($_POST['menu5']) && $_POST['menu5'] && isset($_SESSION['connected']) && $_SESSION['connected']=='success') ) && !isset($_SESSION['ingame'])){
					//On initialise la variable ingame à 1
					$_SESSION['ingame']=1;
					if(!isset($_POST['menu5']) || !$_POST['menu5']){
						$_SESSION['invite']=1;
					}
				}

				else if(isset($_POST['menu4']) && $_POST['menu4'] && !isset($_SESSION['ingame'])){
					?>
					<div id="register_form" class="form_menu">
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
							<input class="menu_submit" type="submit" value="Envoyer">
						</form>
					</div>
					</div>
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
								?><div class="card" id="div_card<?php echo $_SESSION['deck'][$i]->getValue();?>">
									<form action="index.php" method="post">
										<!--La valeur de la carte récupérée dans la classe-->
										<input type="checkbox" 
										name="card_value" 
										value="<?php echo intval($_SESSION['deck'][$i]->getValue())?>" checked hidden>
										<?php 
										//Si la carte est face verso
										if($_SESSION['deck'][$i]->getState()=='verso'){
											?>
											<!--Alors son id vaut '?'-->
											<input type="submit" id="?" value="">
											<?php
										}
										//Sinon si la carte est face recto
										else if($_SESSION['deck'][$i]->getState()=='recto'){
											?>
											<!--Alors son id vaut la valeur de la carte récupérée dans la classe-->
											<input type="submit" id="<?php echo intval($_SESSION['deck'][$i]->getValue())?>" value="" disabled>
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
												if(intval($_SESSION['deck'][$i]->getValue())<=$_SESSION['level']){
													?>
													input[id="<?php echo intval($_SESSION['deck'][$i]->getValue())?>"]{
														background:url('assets/card<?php echo intval($_SESSION['deck'][$i]->getValue())?>');
														background-size:100%;
													}
													<?php
												}
												else if(intval($_SESSION['deck'][$i]->getValue())>$_SESSION['level']){
													$temp_val=intval($_SESSION['deck'][$i]->getValue())-$_SESSION['level'];
													?>
													input[id="<?php echo intval($_SESSION['deck'][$i]->getValue())?>"]{
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
							?><form method="post" action="index.php" class="form_menu">
								<input type="hidden" name="reset">
								<input class="menu_submit" type="submit" value="Relancer">
							</form>
							<?php
							if((!isset($_SESSION['play_count']) || $_SESSION['play_count']==0) && 
								( (isset($_SESSION['AI_play_count']) && $_SESSION['AI_play_count']<1 ) || 
								!isset($_SESSION['AI_play_count']) ) ){?>
							<form method="post" action="index.php" class="form_menu">
								<input type="hidden" name="aiplay" value="1">
								<input class="menu_submit" type="submit" value="Obtenir le perfect score">
							</form>
							<?php
							}
						}
						if(isset($_POST['reset'])){
							?><p>Êtes-vous sûr de vouloir réinitialiser le niveau ?</p>
							<form method="post" action="index.php" class="form_menu">
								<input type="hidden" name="yreset" value=1>
								<input class="menu_submit" type="submit" value="Oui">
							</form>
							<form method="post" action="index.php" class="form_menu">
								<input type="hidden" name="nreset" value=1>
								<input class="menu_submit" type="submit" value="Non">
							</form>
						<?php
						}
					}

					//Si le plateau n'a pas encore été généré
					else{
						?>
						<!--On demande à l'utilisateur les paramètres de la partie-->
						<form method="post" action="index.php" class="form_menu">
							<label for="level">Nombre de paires (de 3 à 12):<br></label>
							<input type="number" name="level" min=3 max=12>
							<br>
							<label for="time">Temps d'affichage (en secondes) :<br></label>
							<input type="number" name="time" min=1 max=5>
							<input type="checkbox" checked hidden name="generate">
							<br>
							<input class="menu_submit" type="submit" value="Générer le niveau">
						</form>
						<?php
					}
				}?>

		</main>

		<footer>
			<?php
			if(isset($_SESSION['connected']) && $_SESSION['connected']=='success' && (!isset($_SESSION['ingame']) || !$_SESSION['ingame'])){
				?><form method="post" action="index.php" class="form_menu">
					<input type="hidden" name="disconnect" value="1">
					<input class="menu_submit" type="submit" value="Se déconnecter">
				</form>
				<?php
			}?>
			<form method="post" action="index.php" class="form_menu">
				<input type="hidden" name="accueil" value="1">
				<input class="menu_submit" type="submit" value="Accueil">
			</form>
			<?php 
			if(isset($_POST['accueil']) && $_POST['accueil'] && isset($_SESSION['ingame']) && $_SESSION['ingame']){
				?>
				<p>La partie en cours sera annulée, continuer ?</p>
				<form method="post" action="index.php" class="form_menu">
					<input type="hidden" name="yaccueil" value=1>
					<input class="menu_submit" type="submit" value="Oui">
				</form>
				<form method="post" action="index.php" class="form_menu">
					<input type="hidden" name="naccueil" value=1>
					<input class="menu_submit" type="submit" value="Non">
				</form>	
			<?php
			}
			//Si le compteur de coup a été initalité
			if(isset($_SESSION['play_count']) && $_SESSION['play_count']){
				if(isset($_POST['aiplay']) && $_POST['aiplay']==1){
					$_SESSION['AI_play_count'] = $_SESSION['play_count'];
					$_SESSION['play_count']	= 0;
				}
				else{
					?><p><?php echo "Compteur de coups de l'utilisateur : " . $_SESSION['play_count'] . "<br>";?></p><?php
				}
			}
			if(isset($_SESSION['AI_play_count']) && in_array($_SESSION['AI_play_count'], range(1,40))){
				?><p>Compteur de coups de L'IA: <?php echo $_SESSION['AI_play_count'] ?>.<br></p><?php
			}
			?>
		</footer>

	</body>
</html>

<?php
	$_SESSION['conn']->close();
	$_SESSION['conn2']->close();
?>