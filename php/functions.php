<?php

	function generate_cards($level){
		if($level){
			$level=2*$level;
			$deck=range(1,$level);
			shuffle($deck);
			shuffle($deck);
			for($i=0;isset($deck[$i]);$i++){
				${"card$i"}=new card();
				${"card$i"}->send_value($deck[$i]);
				$deck[$i]=${"card$i"};
			}
			return $deck;
		}
		else{
			echo "Il y a eut une erreur. Veuillez "?><a href="index.php">Réessayer</a><?php
			return 0;
		}
	}

	function verify_game($deck){
		for($i=0;isset($deck[$i]);$i++){
			if($deck[$i]->get_state()=='verso'){
				return 0;
			}
		}
		echo "La partie est terminée.";
		return 1;
	}

	function unset_cards(&$object, &$card1, &$card2, &$mismatch){
		$mismatch=0;
		for($i=0;isset($object[$i]);$i++){
			if($object[$i]->get_value()==$card1){
				$object[$i]->unselect_card();
			}
			if($object[$i]->get_value()==$card2){
				$object[$i]->unselect_card();
			}
		}
		$card1=NULL;
		$card2=NULL;
	}

	function play_turn(&$counter, &$object, &$sent_value, &$card1, &$card2, &$mismatch, $level, $sec, $page){
		$counter++;
		for($i=0;isset($object[$i]);$i++){
			if($object[$i]->get_value()==$sent_value){
				$object[$i]->select_card();
			}
		}

		if(!isset($card1)){
			$card1=$sent_value;
		}

		else if(!isset($card2)){
			$card2=$sent_value;
			for($i=0;isset($object[$i]);$i++){
				if($object[$i]->get_value()==$sent_value){
					$object[$i]->select_card();
				}
			}
			if(isset($card1) && isset($card2)){
				if( ($card1+$level==$card2) ||
					($card1==$card2+$level) ){
					verify_game($object);
					$card1=NULL;
					$card2=NULL;
				}
				else{
					$mismatch=1;
					?>
					<style>
							form input[type=submit]{pointer-events:none;}
					</style>
					<?php
					header("Refresh: $sec; url=$page");
				}
			}
		}

		else{
			echo "Memory is full. <br>";
		}
	}

?>