<?php

	require_once 'functions.php';

	class card{
		public $state='verso';
		public $value;

		public function send_value($value){
			$this->value=$value;
		}

		public function get_value(){
			return $this->value;
		}

		public function get_state(){
			return $this->state;
		}

		public function select_card(){
			$this->state='recto';
		}

		public function unselect_card(){
			$this->state='verso';
		}
	}

	class user{
		private $id;
		private $login;
		private $password;
		public $score;
		public $ranking;

		public function __construct($login, $password, $vpassword){
			$this->login=$login;
			$this->password=$password;
			$this->vpassword=$vpassword;
		}

		public function create_user(){
			//On se connecte à la base de données
			$db=connect_to('memorydb', 'users');
			//On vérifie l'input utilisateur
			$temp_login=strtolower($this->login);
			$this->login=strtolower($this->login);
			if(preg_match("/([%\$#\*.!&~\"\'{}\+^@=¤:|\/]+)/", $temp_login)){
				die("Les charactères spéciaux authorisés dans le login sont - et _");
			}
			if(strlen($this->login)>40 || strlen($this->password)>40){
				die("Votre login ou votre mot de passe doit comporter 40 charactères au maximum.");
			}
			if($this->password!=$this->vpassword){
				die("Les mots de passe ne correspondent pas.");
			}
			//On vérifie l'existence d'un doublon
			if(look_for($temp_login, $db)){
				die("Ce nom d'utilisateur existe déjà. Veuillez réessayer.");
			}
			//Si tout est OK ( aucun die() ), on ajoute l'utilisateur à la db
			$password=password_hash($this->password, PASSWORD_DEFAULT);
			$stmt=$db->prepare('INSERT INTO `users` (login, password) VALUES (?,?)');
			$stmt->bind_param('ss',$temp_login, $password);
			$stmt->execute();
			echo "Votre compte a bien été créé";
		}

		//Connecte l'utilisateur
		//Cherche l'existence du login et vérifie le mot de passe associé
		public function log_user(&$connect){
			$db=connect_to('memorydb', 'users');
			$temp_login=strtolower($this->login);
			if($result=look_for($temp_login,$db)){
				if(password_verify($this->password,$result['password'])){
					$connect='success';
					return 1;
				}
				else{
					die("Login ou mot de passe incorrect");
				}
			}
			else{
				die("Login ou mot de passe incorrect");
			}
		}

		//Enregistre le score de l'utilisateur
		public function store_game($level,$counter){
			$db=connect_to2('memorydb', 'games');
			$login=strtolower($this->login);
			if(!look_for($login, $db)){
				die("Il y a eut une erreur. Veuillez nous excuser pour la gêne occasionnée.");
			}
			$stmt=$db->prepare("INSERT INTO `games` (login, difficulty, moves) VALUES (?,?,?)");
			$stmt->bind_param('sii', $login, $level, $counter);
			$stmt->execute();
		}

		public function get_login(){
			return $this->login;
		}

		public function get_password(){
			return $this->password;
		}

		public function get_score(){
			return $this->score;
		}

		public function get_ranking(){
			return $this->ranking;
		}
	}
?>