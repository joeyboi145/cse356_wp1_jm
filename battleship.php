<?php
	session_start();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>WP1 Battleship</title>
		<link rel="stylesheet" href="./css/battleship.css">
	</head>
	<body>
        <?php
			$max_rows = 5;
			$max_columns = 7;

			function echo_name_form(){
				echo "<p>Hello stranger, Welcome!</p>";
				echo "<form action='battleship.php' method='post'>";
				echo "Name: <input type='text' name='name'><br>";
				echo "<input class='form_submit_button' type='submit'>";
				echo "</form>";
			}

			function echo_play_again(){
				echo "<form id='reset' action='battleship.php' method='post'>";
				echo "<input type='hidden' name='reset' value='TRUE'>";
				echo "<input class='form_submit_button' form='reset' type='submit' value='Play again'>";
				echo "</form>";
			}

			function echo_hard_reset_button(){
				echo "<form id='hard_reset' action='battleship.php' method='post'>";
				echo "<input type='hidden' name='hard_reset' value='TRUE'>";
				echo "<input class='form_submit_button' form='hard_reset' type='submit' value='hard_reset'>";
				echo "</form>";
			}

			function create_new_board(){
				global $max_rows, $max_columns;
				$new_board = array();
				for ($row = 0; $row < $max_rows; $row++){
					for ($col = 0; $col < $max_columns; $col++){
						array_push($new_board, '?');
					}
				}

				//Generate 2x1, 3x1, and 4x1 ship locations
				$ship_locations = array();
				for ($ship_length = 2; $ship_length < 5; $ship_length++){
					// IF true, choose horizontal position
					$ship_generation_fail = TRUE;
					while ($ship_generation_fail) {
						$ship_generation_fail = FALSE;
						$local_ship_location = array();

						// if true: generate horizontal ship
						if (rand(0,1) == 1){
							$row = rand(0, $max_rows - 1);
							$start = rand(0, $max_columns - $ship_length) + ($row * $max_columns);

							// console("Horizontal " . $ship_length);
							// console("Start: $start");

							for ($i = $start; $i < $start + $ship_length; $i++){
								if (in_array($i, $ship_locations)) {
									$ship_generation_fail = TRUE;
									break;
								}
								array_push($local_ship_location, $i);
							}
							
						// If false, generate vertical ship
						} else {
							$column = rand(0, $max_columns - 1);
							$start = (rand(0, $max_rows - $ship_length) * $max_columns) + $column;

							// console("Verticle " . $ship_length);
							// console("Start: $start");

							for ($i = $start; $i < $start + ($ship_length * $max_columns); $i += $max_columns){
								if (in_array($i, $ship_locations)) {
									$ship_generation_fail = TRUE;
									break;
								}
								array_push($local_ship_location, $i);
							}
						}
					}
					$ship_locations = array_merge($ship_locations, $local_ship_location);
				}
				$_SESSION["ship_locations"] = $ship_locations;
				return $new_board;
			}

			function echo_board($board, $end){
				global $max_rows, $max_columns;
				echo "<table class='battle_table'>";

				/* TO SEE THE SHIP...	*/
				// $ship_locations = $_SESSION["ship_locations"];
				/* In console */
				// for ($i = 0; $i < count($ship_locations); $i++){
				// 	console("Ship location: " . $ship_locations[$i]);
				// }
				/* On the board */
				// if (in_array($cell, $ship_locations) && $cell_value != 'X'){
				// 	$cell_value = '*';
				// }
			
				for ($row = 0; $row < $max_rows; $row++){
					echo "<tr class='battle_row'>";
					for ($col = 0; $col < $max_columns; $col++){
						$cell = ($row * $max_columns) + $col;
						$cell_value = $_SESSION["board"][$cell];
						echo "<td class='battle_cell'>";
						if (!$end && ($cell_value == '?' || $cell_value == '*')) {
							echo "<form name='form$cell' id='form$cell' action='battleship.php' method='post'>";
							echo "<input type='hidden' name='move' value='$cell'>";
							echo "<input form='form$cell' class='battle_empty_cell' type='submit' value='$cell_value'>";
							echo "</form>";
						} else if (!$end) {
							echo $cell_value;
						} else if ($end && !($cell_value == '?' || $cell_value == '*')){
							echo $cell_value;
						}
						echo "</td>";
					}
					echo "</tr>";
				}
				echo "</table>";
			}

			function make_move($move){
				$ship_locations = $_SESSION["ship_locations"];
				if ($_SESSION["board"][$move] == '?') {
					if (in_array($move, $ship_locations) && $_SESSION["board"][$move] != '?'){
						$_SESSION["board"][$move] = 'X';
					} else {
						$_SESSION["board"][$move] = 'O';
					}
					$_SESSION["moves_left"] -= 1;
				}
			}

			function check_win($board){
				$ship_locations = $_SESSION["ship_locations"];
				$win_flag = TRUE;

				// Check if all ships hit
				for ($i = 0; $i < count($ship_locations); $i++){
					$cell = $ship_locations[$i];
					$cell_value = $board[$cell];
					if ($cell_value != 'X') {
						$win_flag = FALSE;
						break;
					}
				}
				return $win_flag;
			}
			
			function console($data) {
				echo "<script>console.log('$data');</script>";
			}

			// Destory session
			if (isset($_POST["hard_reset"])){
				session_destroy();
				session_start();
			}

			if (isset($_SESSION["name"]) || isset($_POST["name"]) || isset($_POST["reset"])){
				if (isset($_POST["reset"])){
					unset($_POST["reset"]);
					unset($_SESSION["move"]);
					$_SESSION["board"] = create_new_board();
					$_SESSION["moves_left"] = ($max_columns * $max_rows * 0.60);
				}
				if (isset($_POST["name"])){
					$_SESSION["name"] = $_POST["name"];
					$_SESSION["board"] = create_new_board();
					$_SESSION["moves_left"] = ($max_columns * $max_rows * 0.60);
				} 
				if (isset($_POST["move"])){
					console(isset($_POST["move"]));
					make_move($_POST["move"]);
					unset($_POST["move"]);
				}

				$board = $_SESSION["board"];
				$moves_left = $_SESSION["moves_left"];
				$win = check_win($board);

				echo "Hello " . $_SESSION["name"] . ", " . date("m/d/Y") . "<br>";
				echo "Moves Left: " . $moves_left . "<br>";
				if ($win) {
					echo "<p>You win!</p>";
					echo_play_again();
				} else if ($moves_left <= 0){
					echo "<p>You lose!</p>";
					echo_play_again();
				}

				$end = $win || $moves_left <= 0;
				echo_board($board, $end);

				/*	Hard button reset  */
				//echo_hard_reset_button();
			} else {
				echo_name_form();
			}


        ?>
    </body>
</html>