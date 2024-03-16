<?php
ini_set('display_errors', 'on');

use Quantik24\PieceQuantik;
use Quantik24\QuantikUIGenerator;
use Quantik24\ArrayPieceQuantik;
use Quantik24\QuantikGame;

require_once 'PieceQuantik.php';
require_once 'PlateauQuantik.php';
require_once 'Player.php';
require_once 'AbstractGame.php';
require_once 'QuantikGame.php';
require_once 'AbstractUIGenerator.php';
require_once 'QuantikUIGenerator.php';

session_start();

$game = QuantikGame::initQuantikGame($_SESSION['game']->getJson());
$position = $_GET['position_piece'];

if($game->currentPlayer == $game->couleursPlayers[0]->getId()){
    $coulActive = PieceQuantik::WHITE;
}else{
    $coulActive = PieceQuantik::BLACK;
}
$page = QuantikUIGenerator::getPagePosePiece($game,$coulActive,$position);
echo $page;

$_SESSION['game']=$game;
$_SESSION['positionPiece']=$position;




