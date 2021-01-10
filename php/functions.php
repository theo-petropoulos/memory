<?php

	//Se connecte à localhost si possible
	//Créé $db si elle n'existe pas puis se connecte à $db
	//Créé la table 'users' si elle n'existe pas encore dans $db
	//Retourne la connexion à $db
	function connect_to($db, $table){
		if(mysqli_connect('localhost','root','',$db)){
			$conn=new mysqli('localhost', 'root', '', $db);
			if(!$conn->query("SELECT * FROM `$table`")){
				$conn->query("
						CREATE TABLE IF NOT EXISTS `$table` (
						id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
						login VARCHAR(255) NOT NULL,
						password VARCHAR(255) NOT NULL,
						score INT(5)
						)");
			}
			return $conn;
		}

		else{
			$conn = mysqli_connect('localhost', 'root', '');

			if($conn){}
			else if(!$conn){
				die("ERROR: Could not connect. " . mysqli_connect_error());
			}
			if(!mysqli_select_db($conn, $db)){
				$sql='CREATE DATABASE memorydb';
				if(!$conn->query($sql)){
					die("ERROR: Could not create database" . mysqli_error() . "<br>");
				}
			}
			if(mysqli_select_db($conn, $db)){
				$conn=mysqli_connect('localhost', 'root', '', $db);
				if(!$conn){
					die("ERROR: Could not connect. " . mysqli_connect_error());
				}
				else{
					$conn->query("
						CREATE TABLE IF NOT EXISTS `$table` (
						id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
						login VARCHAR(255) NOT NULL,
						password VARCHAR(255) NOT NULL,
						score INT(5)
						)");
				}
				return $conn;
			}
		}
	}

	//Se connecte à localhost si possible
	//Créé $db si elle n'existe pas puis se connecte à $db
	//Créé la table 'users' si elle n'existe pas encore dans $db
	//Retourne la connexion à $db
	function connect_to2($db, $table){
		if(mysqli_connect('localhost','root','',$db)){
			$conn=new mysqli('localhost', 'root', '', $db);
			if(!$conn->query("SELECT * FROM `$table`")){
				$conn->query("
						CREATE TABLE IF NOT EXISTS `$table` (
						id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
						login VARCHAR(255) NOT NULL,
						difficulty INT(2) NOT NULL,
						moves INT(3)
						)");
			}
			return $conn;
		}

		else{
			$conn = mysqli_connect('localhost', 'root', '');

			if($conn){}
			else if(!$conn){
				die("ERROR: Could not connect. " . mysqli_connect_error());
			}
			if(!mysqli_select_db($conn, $db)){
				$sql='CREATE DATABASE memorydb';
				if(!$conn->query($sql)){
					die("ERROR: Could not create database" . mysqli_error() . "<br>");
				}
			}
			if(mysqli_select_db($conn, $db)){
				$conn=mysqli_connect('localhost', 'root', '', $db);
				if(!$conn){
					die("ERROR: Could not connect. " . mysqli_connect_error());
				}
				else{
					$conn->query("
						CREATE TABLE IF NOT EXISTS `$table` (
						id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
						login VARCHAR(255) NOT NULL,
						difficulty INT(2) NOT NULL,
						moves INT(3)
						)");
				}
				return $conn;
			}
		}
	}

	//Vérifie l'existance du pseudo dans la db
	function look_for($username, $db){
		$row=$db->query("SELECT * FROM `users`");
		for($i=0;$i<mysqli_num_rows($row);$i++){
			$result=mysqli_fetch_assoc($row);
			if($username==$result['login']){
				return $result;
			}
		}
		return 0;
	}

	//Génération du plateau de jeu
	function generate_cards($level){
		if($level){
			//On donne level=nombre de paires saisi par l'utilisateur
			//Il y aura donc 2*nombre de paires en cartes
			$level=2*$level;
			$deck=range(1,$level);
			//On mélange deux fois les cartes
			shuffle($deck);
			shuffle($deck);
			//On parcourt les cartes tant qu'elles existent
			for($i=0;isset($deck[$i]);$i++){
				//On créé les cartes et on envoie leur valeur dans le deck
				${"card$i"}=new card();
				${"card$i"}->send_value($deck[$i]);
				$deck[$i]=${"card$i"};
			}
			return $deck;
		}
		else{
			echo "Il y a eut une erreur dans la génération du jeu. Veuillez "?><a href="index.php">Réessayer</a><?php
			return 0;
		}
	}

	//Vérifie s'il reste des cartes face verso, permet de définir la fin de partie
	function verify_game($deck){
		for($i=0;isset($deck[$i]);$i++){
			if($deck[$i]->get_state()=='verso'){
				return 0;
			}
		}
		return 1;
	}

	//Retourne les cartes face verso
	function unset_cards(&$deck, &$card1, &$card2, &$mismatch){
		$mismatch=0;
		//On parcourt le deck, dès qu'on trouve la carte 1 et la carte 2, on les déselectionne ( retourne )
		for($i=0;isset($deck[$i]);$i++){
			if($deck[$i]->get_value()==$card1){
				$deck[$i]->unselect_card();
			}
			if($deck[$i]->get_value()==$card2){
				$deck[$i]->unselect_card();
			}
		}
		//On remet à 0 les cartes 1 et 2 en mémoire
		$card1=NULL;
		$card2=NULL;
	}

	//Permet de jouer un tour
	function play_turn(&$counter, &$deck, &$sent_value, &$card1, &$card2, &$mismatch, $level, $sec, $page){
		//On sélectionne la carte jouée dans le deck ( on la retourne face recto )
		for($i=0;isset($deck[$i]);$i++){
			//Si la valeur envoyée par l'utilisateur correspond à la valeur de l'une des cartes du deck
			if($deck[$i]->get_value()==$sent_value){
				$deck[$i]->select_card();
			}
		}

		//S'il n'y a pas de carte 1 en mémoire ( si c'est la première des deux cartes qui est retournée )
		if(!isset($card1)){
			//On attribue la valeur envoyée par l'utilisateur via le click à la carte 1 en mémoire
			$card1=$sent_value;
		}

		//Sinon s'il n'y a pas de carte 2 en mémoire ( si c'est la deuxième des deux cartes qui est retournée )
		else if(!isset($card2)){
			$counter++;
			//On attribue la valeur envoyée par l'utilisateur à la carte 2 en mémoire
			$card2=$sent_value;
			for($i=0;isset($deck[$i]);$i++){
				if($deck[$i]->get_value()==$sent_value){
					$deck[$i]->select_card();
				}
			}
			//On vérifie qu'il y a bien une carte 1 et 2 en mémoire
			if(isset($card1) && isset($card2)){
				//Si les valeurs correspondent ( par exemple sur un jeu à 8 paires, si 1 = 9 )
				if( ($card1+$level==$card2) ||
					($card1==$card2+$level) ){
					//Les cartes restent alors face recto, on vérifie si la partie est terminée
					verify_game($deck);
					//On réinitialise carte 1 et carte 2
					$card1=NULL;
					$card2=NULL;
				}
				//Sinon si les valeurs ne correspondent pas
				else{
					//On incrémente la variable mismatch pour accéder à la condition "les cartes ne correspondent pas" de l'index
					$mismatch=1;
					?>
					<!--On empêche l'utilisateur de cliquer sur une autre carte jusqu'au refresh pour éviter les bugs -->
					<style>
							form input[type=submit]{pointer-events:none;}
					</style>
					<?php
					//On rafraichit la page après X secondes pour laisser l'utilisateur voir la deuxième carte avant qu'elle ne se retourne
					header("Refresh: $sec; url=$page");
				}
			}
		}

		//Message en cas d'erreur si les cartes n'ont pas été sorties de mémoire
		else{
			echo "Memory is full. <br>";
		}
	}

	function ai_play(&$deck, &$memory, $level, $time, &$match_found, &$card1, &$card2, &$play_count, &$mismatch){
		//Tant que la condition de fin de partie est nulle
		$i=$j=1;
		//Tant qu'il existe une carte $i en mémoire et qu'aucune paire n'a été trouvée
		if(isset($memory[$i]) && $memory[$i]){
			while(isset($memory[$i])){
				//On parcourt une deuxième fois la mémoire pour trouver une paire
				while(isset($memory[$j])){
					//Si une paire est trouvée, on initialise la variable match_found pour sortir de la boucle
					//On met en mémoire temporaire les 2 cartes de la paire
					if( ($memory[$i]+$level)==$memory[$j] ||
						($memory[$j]+$level)==$memory[$j] ) {
						$match_found=1;
						$card_1=$i;
						$card_2=$j;
					}
					$j++;
				}
				$i++;
			}
			//Si une paire est trouvée et que la première carte n'est pas encore en mémoire
			if(isset($match_found) && $match_found==1 && !isset($card1)){
				$card=$card_1;
			}
			//Sinon si une paire est trouvée et que la première carte est en mémoire et que la deuxième carte n'est pas en mémoire
			else if(isset($match_found) && $match_found==1 && 
					isset($card1) && $card1 && !isset($card2)){
				$card=$card_2;
				$match_found=NULL;
			}
		}

		//Si aucune paire n'a été trouvée, on joue une carte qui n'a pas encore été jouée
		if(!isset($match_found) || !$match_found){
			for($i=1;isset($memory[$i]);$i++){
			}
			$card=$i;
			$memory[$i]=$card;
		}

		//On joue un tour avec la variable $card définie plus haut
		play_turn(
			$play_count, $deck, $card, 
			$card1, $card2, $mismatch, 
			$level, $time, $_SERVER['PHP_SELF']);
		
		//On vide la mémoire card1 et card2 session toutes les 2 cartes jouées
		if(isset($card1) && $card1 && isset($card2) && $card2){$card1=NULL;$card2=NULL;}
	}

	//Réinitialise la partie aux mêmes paramétrages définis par l'utilisateur, avec un jeu de cartes différent
	function reset_game(&$time, &$deck, &$ingame, &$level, &$counter, &$card1, &$card2, &$memory, &$match){
		$temp_time=$time;
		$temp_level=$level;
		$counter=NULL;$deck=NULL;$ingame=NULL;$card1=NULL;$card2=NULL;$time=NULL;$level=NULL;$memory=NULL;$match=NULL;
		$time=$temp_time;
		$level=$temp_level;
		$ingame=1;
		$deck=generate_cards($level);
	}

	//Réinitialise tout
	function reset_accueil(&$time, &$deck, &$ingame, &$level, &$counter, &$card1, &$card2, &$memory, &$match){
		$counter=NULL;$deck=NULL;$ingame=NULL;$card1=NULL;$card2=NULL;$time=NULL;$level=NULL;$memory=NULL;$match=NULL;
	}

?>