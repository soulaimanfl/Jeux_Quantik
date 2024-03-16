<?php

namespace Quantik24;


abstract class AbstractUIGenerator
{

    public static function getDebutHTML(string $title = "title content"): string
    {
        return '<!DOCTYPE html>
                <html lang="fr">
                <head>
                <meta charset="utf-8" />
                <title>'.$title.'</title>
                <link rel="stylesheet" href="css/style.css" />
                </head>
                <body>
                <h1>'.$title.'</h1>';
    }

    /**
     * @return string
     */
    public static function getFinHTML(): string
    {
        return "</form></div></body>\n</html>";
    }

    /**
     * @param string $message
     * @return string
     */
    public static function getPageErreur(string $message):string
    {
        return "ERREUR";
    }
}