<?php
namespace Quantik24;

require_once 'PieceQuantik.php';
require_once 'PlateauQuantik.php';
require_once 'ActionQuantik.php';

function creerPiece(String $forme, String $couleur): PieceQuantik {
    switch ($forme) {
        case 'cube':
            return $couleur == 'N' ? PieceQuantik::initBlackCube() : PieceQuantik::initWhiteCube();
        case 'cone':
            return $couleur == 'N' ? PieceQuantik::initBlackCone() : PieceQuantik::initWhiteCone();
        case 'cylindre':
            return $couleur == 'N' ? PieceQuantik::initBlackCylindre() : PieceQuantik::initWhiteCylindre();
        case 'sphere':
            return $couleur == 'N' ? PieceQuantik::initBlackSphere() : PieceQuantik::initWhiteSphere();
        default:
            return PieceQuantik::initVoid();
    }
}

/* Construction du jeu */
$plateau = new PlateauQuantik();
$actions = new ActionQuantik($plateau);

/* Ajout des joueurs */
$j1 = "Soulaiman";
$j2 = "Khalil";

/* Initialisation de la variable pour savoir quand est-ce qu'on a un gagnant. */
$perdant = true;

/* Initialisation de la variable pour savoir à qui le tour de jouer. 0 = j1, 1 = j2*/
$tour = 1;

/* Possibilités d'une pièce. */
$checkCoords = ['0', '1', '2', '3'];
$checkFormes = ["cube", "cone", "cylindre", "sphere"];
$checkColors = ['B', 'N'];

while ($perdant) {
    $tourValide = true;

    // Demande des coordonnées une fois
    echo "Veuillez entrer les coordonnées et les caractéristiques de la pièce.\n";

    // Récupération des coordonnées
    $abs = '5';
    while (!in_array($abs, $checkCoords)) {
        echo "Veuillez entrer l'abscisse (0-3): ";
        $abs = fgets(STDIN);
    }
    $abs = intval($abs);

    $ord = '5';
    while (!in_array($ord, $checkCoords)) {
        echo "Veuillez entrer l'ordonnée (0-3): ";
        $ord = fgets(STDIN);
    }
    $ord = intval($ord);

    // Récupération de la forme et de la couleur
    echo "Veuillez entrer la Forme de votre pièce parmi (CUBE, CONE, CYLINDRE, SPHERE): ";
    $forme = trim(strtolower(fgets(STDIN)));

    echo "Veuillez entrer la couleur de votre pièce (B -> Blanche, N -> Noire): ";
    $couleur = trim(strtoupper(fgets(STDIN)));

    // Validation et création de la pièce
    if (in_array($forme, $checkFormes) && in_array($couleur, $checkColors)) {
        $couleur = ($couleur == 'B') ? 0 : 1;
        $pieceCreee = creerPiece($forme, $couleur);

        // Si la pose est valide, on met à jour le plateau et le tour
        if ($actions->isValidePose($ord, $abs, $pieceCreee)) {
            $actions->posePiece($ord, $abs, $pieceCreee);

            if ($actions->isColWin($abs) || $actions->isRowWin($ord) || $actions->isCornerWin(PlateauQuantik::getCornerFromCoord($abs, $ord))) {
                $perdant = false;
                $gagnant = ($tour == 0) ? $j1 : $j2;
                echo "\nFélicitations ! $gagnant a gagné !";
            } else {
                echo "\n--------- Prochain Tour --------- \n";
            }

            // Changement de tour
            $tour = ($tour + 1) % 2;
        } else {
            echo "La pose de la pièce est invalide. Veuillez réessayer.\n";
        }
    } else {
        echo "La forme ou la couleur entrée n'est pas valide. Veuillez réessayer.\n";
    }
}