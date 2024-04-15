<?php

function infoGeneral(){
    echo "\n";
    echo "***MANUAL D'ÚS***\n";
    echo "\n";
    
    echo "Tasques disponibles:\n";
    echo "add: Permet afegir una tasca\n";
    echo "list: Mostra per pantalla totes les tasques disponibles\n";
    echo "mark: Pots modificar una tasca\n";
    echo "delete: Tens la opcio de eliminar qualsevol tasca\n";
    echo "\n";
    echo "SINTAXIS CORRECTA:\n";

    echo "-a --action           Acció a realitzar\n";
    echo "-t --title            Titol de la tasca a ingresar\n";
    echo "-d --description      Descripcio de la tasca\n";

    echo "EXEMPLE: php <document.php -a add --title deures -d <descripcio tasca>\n";
}

function infoadd(){
    echo "\n";
    echo "Per a afegir una tasca, es necessari posar els següents arguments: \n";
    echo "php <fitxer.php> (-a/--action) <add> (-t/--title)<titol de la tasca> (-d/--description)<contingut>\n";
    echo "\n";
}

function infoDelete(){
    echo "\n";
    echo "Per a esborrar una tasca, es necessari posar els següents arguments: \n";
    echo "php <fitxer.php> (-a/--action) <delete> (-t/--title)<id de la tasca>\n";
    echo "\n";
}

function infoList(){
    echo "\n";
    echo "Per a esborrar una tasca, es necessari posar els següents arguments: \n";
    echo "php <fitxer.php> (-a/--action) <list>\n";
    echo "EXEMPLE: php <document.php> list\n";
}

function infoMarcar(){
    echo "\n";
    echo "Per a marcar una tasca com a finalitzada: \n";
    echo "Hauras de escriure la seguent estructura: \n";
    echo "php <fitxer.php> (-a/--action) <mark> (-t/--title)<id de la tasca> (-d/--description)<done>\n";
}

?>