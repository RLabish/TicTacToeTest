<?php

namespace App\Game;

class TicTacToe
{

    private $table = [];


    function __construct($table = [])
    {
        if ($table) {
            $this->table = $table;
        } else {
            $this->table = $this->initTable();
        }
    }

    private function initTable()
    {
        $table = [];
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $table[$i][$j] = _;
            }
        }
        return $table;
    }

    function getTable()
    {
        return $this->table;
    }


    function isGameCompleted()
    {
        $winner = $this->getWinner();
        if ($winner !== _) {
            return true;
        }

        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                if ($this->table[$i][$j] === _) {
                    return false;
                }
            }
        }
        return true;
    }


    function getWinner()
    {

        if ($winner = $this->checkHorizontal()) {
            return $winner;
        }

        if ($winner = $this->checkVertical()) {
            return $winner;
        }

        if ($winner = $this->checkDiagonal()) {
            return $winner;
        }
    }

    private function checkHorizontal()
    {
        for ($i = 0; $i < 3; $i++) {
            $winner = $this->table[$i][0];

            for ($j = 0; $j < 3; $j++) {
                if ($this->table[$i][$j] != $winner) {
                    $winner = null;
                    break;
                }
            }
            if ($winner !== null) {
                break;
            }
        }
        return $winner;
    }

    private function checkVertical()
    {
        for ($i = 0; $i < 3; $i++) {
            $winner = $this->table[0][$i];

            for ($j = 0; $j < 3; $j++) {
                if ($this->table[$j][$i] != $winner) {
                    $winner = null;
                    break;
                }
            }
            if ($winner !== null) {
                break;
            }
        }
        return $winner;
    }

    private function checkDiagonal()
    {

        $winner = $this->table[0][0];
        for ($i = 0; $i < 3; $i++) {
            if ($this->table[$i][$i] != $winner) {
                $winner = null;
                break;
            }
        }

        if ($winner === null) {
            $winner = $this->table[0][2];
            for ($i = 0; $i < 3; $i++) {
                if ($this->table[$i][2 - $i] != $winner) {
                    $winner = null;
                    break;
                }
            }
        }

        return $winner;
    }

}
