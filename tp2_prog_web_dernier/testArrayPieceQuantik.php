<?php
namespace Quantik24;

require_once 'PieceQuantik.php';
require_once 'ArrayPieceQuantik.php';


$apq = ArrayPieceQuantik::initPiecesNoires();
for ($i=0; $i<count($apq); $i++)
    echo $apq[$i];
echo "\n";
$apq = ArrayPieceQuantik::initPiecesBlanches();
for ($i=0; $i<count($apq); $i++)
    echo $apq[$i];
echo "\n";



/* ** TRACE d'éxécution de ce programme
(Co:B)(Co:B)(Cu:B)(Cu:B)(Cy:B)(Cy:B)(Sp:B)(Sp:B)
(Co:W)(Co:W)(Cu:W)(Cu:W)(Cy:W)(Cy:W)(Sp:W)(Sp:W)
** */
