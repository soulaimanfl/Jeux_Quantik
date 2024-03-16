<?php

use Quantik24\ArrayPieceQuantik;
use Quantik24\PDOQuantik;
use Quantik24\QuantikGame;

ini_set('display_errors', 'on');
require_once 'PDOQuantik.php';
require_once 'PieceQuantik.php';
require_once 'PlateauQuantik.php';
require_once 'Player.php';
require_once 'AbstractGame.php';
require_once 'QuantikGame.php';
require_once 'AbstractUIGenerator.php';
require_once 'QuantikUIGenerator.php';
require_once 'env/db.php';

session_start();

// Vérifier si la clé "player" est définie dans la session
if (!isset($_SESSION['player'])) {
    // Rediriger vers la page de login si le joueur n'est pas identifié
    header("Location: login.php");
    exit();
}

// Initialiser la connexion PDO
PDOQuantik::initPDO($_ENV['sgbd'], $_ENV['host'], $_ENV['database'], $_ENV['user'], $_ENV['password']);

// Récupérer le joueur depuis la session
$player = $_SESSION['player'];
$nom = ""; // Initialisez la variable $nom

// Vérifiez si $player est défini et récupérez son nom
if ($player !== null) {
    $nom = $player->getName();
}

try {
    // Récupérer les jeux du joueur
    $gamesPlayer = [];
    if ($nom !== "") {
        $gamesPlayer = PDOQuantik::getAllGameQuantikByPlayerName($nom);
    } else {
        // Gérer le cas où $nom n'est pas défini ou vide
    }
} catch (Exception $e) {
    echo "Une erreur s'est produite : " . $e->getMessage();
}

// Récupérer tous les jeux
$allgames = PDOQuantik::getAllGameQuantik();

// Initialiser les tableaux pour les jeux en attente, en cours et terminés
$enAttente = array();
$enCours = array();
$finito = array();

// Parcourir les jeux du joueur et les répartir en fonction de leur statut
foreach ($gamesPlayer as $game) {
    switch ($game->gameStatus) {
        case 'initialized':
            $enAttente[] = $game;
            break;
        case 'waitingForPlayer':
            $enCours[] = $game;
            break;
        case 'finished':
            $finito[] = $game;
            break;
    }
}

// Ajouter les jeux non attribués à un joueur dans les jeux en attente
foreach ($allgames as $game) {
    switch ($game->gameStatus) {
        case 'initialized':
            if ($game->currentPlayer != $player->getId()) {
                $enAttente[] = $game;
            }
    }
}

// Construction de la page HTML
$page = "<!DOCTYPE html><html lang='fr'><head><meta charset='utf-8' /><title>Quantik</title>
<link rel='stylesheet' href='css/style.css' /></head>";
$page .= "<body><h1>Jeu Quantik</h1><h2>Salon jeux de " . $nom . "</h2>";

// Formulaire pour créer une nouvelle partie
$page .= "<form action = " . $_SERVER['PHP_SELF'] . " method='post'>
		<div class='partie'>
		<button type='submit' name='creerPartie'><img src='css/images/play.svg' /></button>
		</div>";

// Affichage des jeux en cours
$page .= "<h3>Les parties en cours</h3>";
$page .= "<div class='partie'><table>";
foreach ($enCours as $partie) {
    $page .= "<tr>";
    if ($partie->currentPlayer == $player->getId()) {
        $page .= "<td class='game'>
					<button type='submit' name='Jouer' value='" . $partie->getId() . "'>
					<img src='css/images/attente.svg'/>
					</button>
					</td>
					<td class='game'>$partie</td>";
    } else {
        $page .= "<td class='game'>
					<button type='submit' name='Consulter' value='" . $partie->getId() . "'>
					<img src='css/images/attente.svg'/>
					</button>
					</td>
					<td class='game'>$partie</td>";
    }
    $page .= "</tr>";
}
$page .= "</table></div>";

// Affichage des jeux en attente
$page .= "<h3>Les parties en attente d'autre joueur</h3>";
$page .= "<div class='partie'><table>";
foreach ($enAttente as $partie) {
    $page .= "<tr>";
    if ($partie->currentPlayer == $player->getId()) {
        $page .= "<td class='game'>
					<button type='submit' name='attente' disabled>
					<img src='css/images/attente.svg' />
					</button>
					</td>
					<td class='game'>$partie</td>";
    } else {
        $page .= "<td class='game'>
					<button type='submit' name='rejoindrePartie' value='" . $partie->getId() . "'>
					<img src='css/images/add-user.svg' />
					</button>
					</td>
					<td class='game'>$partie</td>";
    }
    $page .= "</tr>";
}
$page .= "</table></div>";

// Affichage des jeux terminés
$page .= "<h3> Les parties terminées</h3>";
$page .= "<div class='partie'><table>";
foreach ($finito as $partie) {
    $page .= "<tr>";
    $page .= "<td class='game'>
			<button type='submit' name='Consulter' value='" . $partie->getId() . "'>
			<img src='css/images/histo.svg' />
			</button>
			</td>
			<td class='game'>$partie</td>
		<tr>";
}
$page .= "</table></div><br/>";

// Bouton pour déconnecter
$page .= "<div class='partie'><button type='submit' name='deconnecter'><img src='css/images/exit.svg'/></button></div>";

$page .= "</form>  ";
$page .= "</body></html>";

echo $page;

// Traiter les actions du formulaire
    // Créer une nouvelle partie


    // Déconnexion
    // Code à ajouter ici...


    // Rejoindre une partie
    // Code à ajouter ici...

    // Joindre une partie pour jouer
    // Code à ajouter ici...


    // Consulter une partie
    // Code à ajouter ici...


if(isset($_REQUEST['creerPartie'])){
    $players = array();
    $players[0] = $player;
    $game = new QuantikGame(0, $players);

    PDOQuantik::createGameQuantik($nom,$game->getJson());
    $id = PDOQuantik::getLastInsertId('quantikgame_gameid_seq');
    $game->setId($id);
    $game->setStatus('initialized');
    PDOQuantik::saveGameQuantik('initialized',$game->getJson(),$id);
    header('Refresh: 0; url=index.php');
}
if(isset($_REQUEST['deconnecter'])){
    header('Location: login.php');
}
if(isset($_REQUEST['rejoindrePartie'])){
    PDOQuantik::addPlayerToGameQuantik($nom,$_REQUEST['rejoindrePartie']);
    header('Refresh: 0; url=index.php');
}
if(isset($_REQUEST['Jouer'])){
    $_SESSION['game'] = PDOQuantik::getGameQuantikById($_REQUEST['Jouer']);
    header('Location: choisirPiece.php');
}
if(isset($_REQUEST['Consulter'])){
    $_SESSION['game'] = PDOQuantik::getGameQuantikById($_REQUEST['Consulter']);
    header('Location: consulter.php');
}
