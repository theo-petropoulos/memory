<?php

	require_once 'php/card.php';
	require_once 'php/functions.php';

	$page = $_SERVER['PHP_SELF'];
	$sec = "1";
	$level=6;

	if(!isset($_SESSION)){
		session_start();
	}

	if(isset($_SESSION['mismatch']) && $_SESSION['mismatch'] && isset($_SESSION['card1']) && isset($_SESSION['card2'])){
		unset_cards($_SESSION['play_count'], $_SESSION['deck'], $_SESSION['card1'], $_SESSION['card2'], $_SESSION['mismatch']);
	}

	if(isset($_POST['check_card']) && $_POST['check_card']){
		play_turn(
			$_SESSION['play_count'], $_SESSION['deck'], $_POST['card_value'], 
			$_SESSION['card1'], $_SESSION['card2'], $_SESSION['mismatch'], 
			$level, $sec, $page);
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
			if(isset($_SESSION['deck']) && $_SESSION['deck']){
				if(isset($_SESSION['play_count']) && $_SESSION['play_count']==0){
					echo "Compteur de coups : " . $_SESSION['play_count'] . "<br>";
				}
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
									<input type="submit" value="?">
									<?php
								}
								else if($_SESSION['deck'][$i]->get_state()=='recto'){
									?>
									<input type="submit" value="<?php echo intval($_SESSION['deck'][$i]->get_value())?>" disabled>
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
				$_SESSION['deck']=generate_cards($level);
				$_SESSION['play_count']=0;
				?>
				<form method="post" action="index.php">
					<input type="checkbox" checked hidden name="generate">
					<input type="submit" value="Générer le niveau">
				</form>
				<?php
			}?>

		</main>

	</body>
</html>