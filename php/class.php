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
			//On vérifie l'input utilisateur
			$db=connect_to('memorydb');
			if(preg_match("/([%\$#\*.!&~\"\'{}\+^@=¤:|\/]+)/", $this->login)){
				die("Les charactères spéciaux authorisés dans le login sont - et _");
			}
			if(strlen($this->login)>30 || strlen($this->password)>250){
				die("Votre login doit comporter 30 charactères au maximum.");
			}
			if($this->password!=$this->vpassword){
				die("Les mots de passe ne correspondent pas.");
			}
			//On vérifie l'existence d'un doublon
			if(look_for($this->login, $db)){
				die("Ce nom d'utilisateur existe déjà. Veuillez réessayer.");
				header("Refresh: 3; url=index.php");
			}
			//Si tout est OK ( aucun die() ), on ajoute l'utilisateur à la db
			$login=$this->login; $password=password_hash($this->password, PASSWORD_DEFAULT);
			$stmt=$db->prepare('INSERT INTO `users` (login, password) VALUES (?,?)');
			$stmt->bind_param('ss',$login, $password);
			$stmt->execute();
			echo "Votre compte a bien été créé";
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