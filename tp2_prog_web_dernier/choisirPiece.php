<?php


namespace Quantik24;
ini_set('display_errors', 'on');

require_once 'PieceQuantik.php';
require_once 'PlateauQuantik.php';

require_once 'Player.php';
require_once 'AbstractGame.php';
require_once 'QuantikGame.php';
require_once 'AbstractUIGenerator.php';
require_once 'QuantikUIGenerator.php';
require_once 'PDOQuantik.php';
require_once 'env/db.php';


session_start();

$game = QuantikGame::initQuantikGame($_SESSION['game']->getJson());

if($game->currentPlayer == $game->couleursPlayers[0]->getId()){
    $coulActive = PieceQuantik::WHITE;
}else{
    $coulActive = PieceQuantik::BLACK;
}

$page = QuantikUIGenerator::getPageSelectionPiece($game,$coulActive);
echo $page;

$_SESSION['game'] = $game;

