<?php

class TicTacToe {

    public $spaces = array(
        'A1' => null,
        'B1' => null,
        'C1' => null,
        'A2' => null,
        'B2' => null,
        'C2' => null,
        'A3' => null,
        'B3' => null,
        'C3' => null
    );

    public $victory_conditions = array(
        array('A1', 'B1', 'C1'),
        array('A2', 'B2', 'C2'),
        array('A3', 'B3', 'C3'),
        array('A1', 'A2', 'A3'),
        array('B1', 'B2', 'B3'),
        array('C1', 'C2', 'C3'),
        array('A1', 'B2', 'C3'),
        array('A3', 'B2', 'C1')
    );

    public $players = array(
        'X' => null,
        'O' => null
    );

    public $pvc = true;	// player vs computer
    public $turns = 0;
    public $winner = null;

    function __construct() {
        print "Number of players (1 or 2)? ";
        $num_players = $this->getPlayerInput();
        if ($num_players == 2) $this->pvc = false;
        $this->assignPlayers();
    }

    function playRound() {
        foreach($this->players as $piece=>$name) {
            if ($name === 'Computer') {
                $this->computerMove();
            } else {
                $this->playerMove();
            }
        }
    }

    function assignPlayers() {
        foreach($this->players as $piece=>&$name) {
            print "What's your name? ";
            $name = $this->getPlayerInput();
            if ($this->pvc) {
                $keys = array_keys($this->players);
                $this->players[$keys[1]] = 'Computer';
                break;
            }
        }
    }

    /**
     *	Returns the player name
     *	@param $index array index of 0 or 1
     *	@return string name of player
     */
    function getPlayerNameById($index) {
        if ($num != 0 && $num != 1) throw new Exception("Invalid input. Must be 0 or 1");
        $keys = array_keys($this->players);
        return $this->players[$keys[$index]];
    }

    /**
     *	Returns the current player's name
     *	@return string name of player
     */
    function getCurrentPlayerName() {
        $num = $this->turns % 2;
        $keys = array_keys($this->players);
        return $this->players[$keys[$num]];
    }

    /**
     *	Returns the current player's piece
     *	@return string X or O
     */
    function getCurrentPlayerPiece() {
        $num = $this->turns % 2;
        $keys = array_keys($this->players);
        return $keys[$num];
    }

    /**
     *	Prints the game board
     */
    function printBoard() {

        print "a b c \n";

        $i=0;
        foreach($this->spaces as $k=>$v) {

            if (is_null($v) && $i>5) {
                print ' ';
            } elseif (is_null($v)) {
                print '_';
            } else {
                print $v;
            }

            if ($i%3 <= 1) {
                print "|";
            }

            if ($i == 2) {
                print " 1\n";
            } elseif ($i == 5) {
                print " 2\n";
            } elseif ($i == 8) {
                print " 3\n";
            }

            $i++;
        }
    }

    /**
     *	Player move round
     */
    function playerMove() {
        $this->printBoard();

        $move = $this->getPlayerMove();
        while(!$this->isValidMove($move)) {
            print "That's not a valid input. Try again.\n";
            $move = $this->getPlayerMove();
        }
        $player_piece = $this->getCurrentPlayerPiece();
        $this->spaces[$move] = $player_piece;
        $this->checkVictoryConditions($player_piece);
        $this->turns++;
    }

    /**
     *	Computer move round
     */
    function computerMove() {
        $empty_spaces = $this->getSpacesForType(null);
        $rand = rand(0, count($empty_spaces)-1);
        $key = $empty_spaces[$rand];
        $computer_piece = array_keys($this->players, 'Computer');
        $computer_piece = $computer_piece[0];
        $this->spaces[$key] = $computer_piece;
        $this->checkVictoryConditions($computer_piece);
        $this->turns++;
    }

    /**
     *	Get input from the player
     *	@return string
     */
    function getPlayerInput() {

        return trim(fgets(STDIN));
    }

    /**
     *	Get the player's next move
     *	@return string
     */
    function getPlayerMove() {
        $player_name = $this->getCurrentPlayerName();
        print "$player_name's move? ";
        return ucwords($this->getPlayerInput());
    }

    /**
     *	Check that the input matches available space keys and is empty.
     *	@return boolean
     */
    function isValidMove($move) {
        return (array_key_exists($move, $this->spaces) && is_null($this->spaces[$move]) ? true : false);
    }

    /**
     *	Returns an array of spaces with certain occupant
     *	@param $type null|'X'|'O'
     *	@return array
     */
    function getSpacesForType($type) {
        if ($type != null && $type != 'X' && $type != 'O') throw new Exception ("Invalid input. Must be one of null, 'X' or 'O'");
        return array_keys($this->spaces, $type);
    }

    /**
     *	Checks if any victory condition is met by the current player
     *	or if game should be declared a tie.
     *	@param $piece 'X'|'O'
     */
    function checkVictoryConditions($piece) {
        $player_occupied_spaces = $this->getSpacesForType($piece);

        foreach($this->victory_conditions as $vc) {
            if ($vc == array_intersect($vc, $player_occupied_spaces)) {
                $this->winner = $this->players[$piece];
                $this->printBoard();
                $victory_message = $this->winner . " is the winner!\n";
                print strtoupper($victory_message);
                die();
            }
        }

        if ($this->turns == 8) {
            print "It's a tie!\n";
            die();
        }
    }

    /**
     *	Check if the game has a winner.
     *	@return boolean
     */
    function hasWinner() {
        return (is_null($this->winner) ? false: true);
    }

}

$game = new TicTacToe();

while (!$game->hasWinner()) {
    $game->playRound();
}

?>
