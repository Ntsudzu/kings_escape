<?php
session_start();

$r1 = intval($_POST['r1']);
$c1 = intval($_POST['c1']);
$r2 = intval($_POST['r2']);
$c2 = intval($_POST['c2']);

$board = $_SESSION['board'];

// validate: king only moves 1 step
if ($board[$r1][$c1] == "K" && abs($r2-$r1) <= 1 && abs($c2-$c1) <= 1) {
    if ($board[$r2][$c2] == "" || $board[$r2][$c2] == "E") {
        $board[$r1][$c1] = "";
        $board[$r2][$c2] = "K";
        $_SESSION['board'] = $board;
        $_SESSION['turn']++;

        // spawn random enemy each turn
        if ($_SESSION['turn'] % 2 == 0) {
            $spawned = false;
            while (!$spawned) {
                $r = rand(1,8); $c = rand(1,8);
                if ($board[$r][$c] == "") {
                    $board[$r][$c] = "P"; $spawned=true;
                }
            }
            $_SESSION['board'] = $board;
        }
    }
}

// reload board
include("index.php");
