<?php

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

?>