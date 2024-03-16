<?php

namespace Quantik24;
use Quantik24\ArrayPieceQuantik;


require_once 'PieceQuantik.php';
require_once 'PlateauQuantik.php';
require_once 'Player.php';
require_once 'AbstractGame.php';
require_once 'QuantikGame.php';
require_once 'AbstractUIGenerator.php';
require_once 'QuantikUIGenerator.php';


session_start();
$game =$_SESSION['game'];
if($game->currentPlayer == $game->couleursPlayers[0]->getId()){
    $coulActive = PieceQuantik::WHITE;
}else{
    $coulActive = PieceQuantik::BLACK;
}
echo QuantikUIGenerator::getPageVictoire($game,$coulActive);