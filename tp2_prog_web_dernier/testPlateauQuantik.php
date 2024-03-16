<?php
namespace Quantik24;

// Include necessary classes
require_once 'ArrayPieceQuantik.php';
require_once 'PlateauQuantik.php';
require_once 'PieceQuantik.php';

// Crée une nouvelle instance de PlateauQuantik
$plateau = new PlateauQuantik();

// Place quelques pièces sur le plateau
$plateau->setPiece(0, 0, PieceQuantik::initBlackCube());
$plateau->setPiece(1, 1, PieceQuantik::initWhiteCone());
$plateau->setPiece(2, 2, PieceQuantik::initBlackSphere());
$plateau->setPiece(3, 3, PieceQuantik::initWhiteCylindre());

// Affiche la représentation en chaîne du plateau
echo "Plateau String Representation:" . PHP_EOL;
echo $plateau->__toString() . PHP_EOL;

// Obtient et affiche une ligne du plateau
$rowIndex = 2;
$row = $plateau->getRow($rowIndex);
echo "Row at index $rowIndex: ";
foreach ($row as $piece) {
    echo $piece . ", ";
}
echo PHP_EOL;

// Obtient et affiche une colonne du plateau
$colIndex = 1;
$col = $plateau->getCol($colIndex);
echo "Column at index $colIndex: ";
foreach ($col as $piece) {
    echo $piece . ", ";
}
echo PHP_EOL;

// Obtient et affiche une pièce spécifique du plateau
$piece = $plateau->getPiece(1, 1);
echo "Piece at position (1, 1): " . $piece . PHP_EOL;

// Ajoute un test pour vos méthodes supplémentaires si nécessaire

?>
