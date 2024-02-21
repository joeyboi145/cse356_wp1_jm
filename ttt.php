<!DOCTYPE html>
<html>
	<head>
	<title>WP1 Tic-Tac-Toe</title>
		<link rel="stylesheet" href="./css/ttt.css">
	</head>

	<body>
		<?php

		function echo_GET_form(){
			echo "<p>Hello stranger, Welcome!</p>";
			echo "<form action='ttt.php'>";
			echo "Name: <input type='text' name='name'><br>";
			echo "<input class='form_submit_button' type='submit'>";
			echo "</form>";
		}

		function ttt_cellStrIndex($board, $cell){
			$cell_pointer = 0;
			$str_pointer = 0;
			while ($cell_pointer < $cell){
				$char = substr($board, $str_pointer, 1);
				if ($char == ' ') {
					$cell_pointer++;
				}
				$str_pointer++;
			}
			return $str_pointer;
		}

		function ttt_getCellValue($board, $cell){
			$str_index = ttt_cellStrIndex($board, $cell);
			if ($str_index == strlen($board)) 
				return ' ';
			$char = substr($board, $str_index, 1);
			return $char;
		}

		function ttt_changeCellValue($board, $cell, $value){
			$new_board = "";
			$str_index = ttt_cellStrIndex($board, $cell);
			if ($str_index == strlen($board)) {
				$new_board = $board . $value;
			} else { 
				$new_board = substr($board, 0, ($str_index)) . $value . substr($board, $str_index, (strlen($board) - $str_index));
			}
			return $new_board;
		}

		function ttt_check_win($board){
			$board_array = array();
			$spots_used = 0;
			
			for ($cell = 0; $cell < 9; $cell++){
				$value = ttt_getCellValue($board, $cell);
				if (!($value == ' ')) $spots_used++;
				array_push($board_array, ttt_getCellValue($board, $cell));
			}
			
			// Check rows for win condition
			for ($row = 0; $row < 9; $row += 3){
				$X = 0;
				$O = 0;
				for ($i = $row; $i < $row + 3; $i++){
					$value = $board_array[$i];
					if ($value == 'X') $X++;
					else if ($value == 'O') $O++;
				}
				if ($X == 3) 		return 'X';
				else if ($O == 3)	return 'O';
			}
			
			// Check columns for win condition
			for ($column = 0; $column < 3; $column++){
				$X = 0;
				$O = 0;
				for ($i = $column; $i < 9; $i += 3){
					$value = $board_array[$i];
					if ($value == 'X') $X++;
					else if ($value == 'O') $O++;
				}
				if ($X == 3) 		return 'X';
				else if ($O == 3)	return 'O';
			}
			
			$X = 0;
			$O = 0;
			// Check diagonals for win condition
			for ($i = 0; $i < 9; $i += 4){
				$value = $board_array[$i];
				if ($value == 'X') $X++;
				else if ($value == 'O') $O++;
			}
			if ($X == 3) 		return 'X';
			else if ($O == 3)	return 'O';
			
			$X = 0;
			$O = 0;
			for ($i = 2; $i < 8; $i += 2){
				$value = $board_array[$i];
				if ($value == 'X') $X++;
				else if ($value == 'O') $O++;
			}
			if ($X == 3) 		return 'X';
			else if ($O == 3)	return 'O';
			
			if ($spots_used == 9) 	return 'T';
			else 					return '-';
			
		}

		function ttt_make_move($board){
			$possible_moves = array();
			for ($i = 0; $i < 9; $i++){
				if (ttt_getCellValue($board, $i) == ' ') {
					array_push($possible_moves, $i);
				}
			}

			$index = rand(0, count($possible_moves) - 1);
			return ttt_changeCellValue($board, $possible_moves[$index], 'O');
		}
		
		function echo_ttt_board($name, $board, $won){
			echo "<table class='ttt_table'><br>";
			for ($row = 0; $row < 3; $row++){
				if ($row == 1){
					echo "<tr class='ttt_middle_row ttt_row'>";
				} else {
					echo "<tr class='ttt_row'>";
				}

				for ($column = 0; $column < 3; $column++){
					if ($column == 1){
						echo "<td class='ttt_middle_column ttt_cell'>";
					} else {
						echo "<td class='ttt_cell'>";
					}

					$cell = ($row * 3) + ($column);
					$cell_value = ttt_getCellValue($board, $cell);

					if ($cell_value == ' ' && !$won){
						$new_board = ttt_changeCellValue($board, $cell, "X");		
						$winner = ttt_check_win($new_board);
						
						if ($winner == '-'){
							$new_board = ttt_make_move($new_board);
						}

						$new_board = str_replace(" ", "+", $new_board);
						$url = "./ttt.php?name=$name&board=$new_board";
						echo "<a class='ttt_empty_cell' href='$url' target='_self'>-</a>";
					} else {
						echo "$cell_value";
					}
					echo "</td>";
				}
				echo "</tr>";
			}
			echo "</table>";
		}

		function echo_play_again($name){
			echo "<form action='/ttt.php'>";
			echo "<input type='hidden' name='name' value='$name' />";
			echo "<input class='form_submit_button' type='submit' value='Play again'>";
		}
		
		function debug_to_console($data) {
			echo "<script>console.log('$data');</script>";
		}
		
		
		if (isset($_GET["name"])) {
			$name = $_GET["name"];

			echo "Hello $name, " . date("m/d/Y") . "<br>";

			$board = "        ";
			if (isset($_GET["board"])) {
				$board = $_GET["board"];
			}

			$winner = ttt_check_win($board);
			// echo_ttt_board($name, $board, $winner != '-');
			switch ($winner){
				case "X":
					echo "<p>You won!</p>";
					echo_play_again($name);
					break;
				case "O":
					echo "<p>I won!</p>";
					echo_play_again($name);
					break;
				case "T":
					echo "<p>WINNER: NONE.  A STRANGE GAME.  THE ONLY WINNING MOVE IS NOT TO PLAY.</p>";
					echo_play_again($name);
					break;
				default:
					break;
			}
			echo_ttt_board($name, $board, $winner != '-');

		} else {
			echo_GET_form();
		}
		?>
	</body>
</html>
