<?php
	session_start();

	if(isset($_POST['disconnect']) && $_POST['disconnect']){
		session_destroy();
		header("Refresh:0");
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

		<section id="main_screen">

			<?php
				if( (!isset($_POST['menu1']) || !$_POST['menu1']) &&
					(!isset($_POST['menu2']) || !$_POST['menu2']) &&
					(!isset($_POST['menu3']) || !$_POST['menu3']) ){
					?>
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

				else if(isset($_POST['menu1']) && $_POST['menu1']){
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

				else if(isset($_POST['menu2']) && $_POST['menu2']){
					?>

					<table>
						<tr>
							<td>
								<div>
									<form method="post" action="index.php">
										<input type="checkbox" id="cardx" name="cardx" checked hidden>
										<input type="submit" value="">
									</form>
								</div>
							</td>
							<td>
								<div>
									<form method="post" action="index.php">
										<input type="checkbox" id="cardx" name="cardx" checked hidden>
										<input type="submit" value="">
									</form>
								</div>
							</td>
							<td>
								<div>
									<form method="post" action="index.php">
										<input type="checkbox" id="cardx" name="cardx" checked hidden>
										<input type="submit" value="">
									</form>
								</div>
							</td>
							<td>
								<div>
									<form method="post" action="index.php">
										<input type="checkbox" id="cardx" name="cardx" checked hidden>
										<input type="submit" value="">
									</form>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div>
									<form method="post" action="index.php">
										<input type="checkbox" id="cardx" name="cardx" checked hidden>
										<input type="submit" value="">
									</form>
								</div>
							</td>
							<td>
								<div>
									<form method="post" action="index.php">
										<input type="checkbox" id="cardx" name="cardx" checked hidden>
										<input type="submit" value="">
									</form>
								</div>
							</td>
							<td>
								<div>
									<form method="post" action="index.php">
										<input type="checkbox" id="cardx" name="cardx" checked hidden>
										<input type="submit" value="">
									</form>
								</div>
							</td>
							<td>
								<div>
									<form method="post" action="index.php">
										<input type="checkbox" id="cardx" name="cardx" checked hidden>
										<input type="submit" value="">
									</form>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div>
									<form method="post" action="index.php">
										<input type="checkbox" id="cardx" name="cardx" checked hidden>
										<input type="submit" value="">
									</form>
								</div>
							</td>
							<td>
								<div>
									<form method="post" action="index.php">
										<input type="checkbox" id="cardx" name="cardx" checked hidden>
										<input type="submit" value="">
									</form>
								</div>
							</td>
							<td>
								<div>
									<form method="post" action="index.php">
										<input type="checkbox" id="cardx" name="cardx" checked hidden>
										<input type="submit" value="">
									</form>
								</div>
							</td>
							<td>
								<div>
									<form method="post" action="index.php">
										<input type="checkbox" id="cardx" name="cardx" checked hidden>
										<input type="submit" value="">
									</form>
								</div>
							</td>
						</tr>
					</table>

					<?php
					}
				?>
		</section>

		<footer>
			<h3>Crédits - etc</h3>
		</footer>

	</body>

</html>