
<?php

include 'conn.php';

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
    echo "php <fitxer.php> (-a/--action) <add> (-t/--title) <titol de la tasca> (-d/--description) <contingut>\n";
    echo "\n";
}

function infoDelete(){
    echo "\n";
    echo "Per a esborrar una tasca, es necessari posar els següents arguments: \n";
    echo "php <fitxer.php> (-a/--action) <delete> (-t/--title) <id de la tasca>\n";
    echo "\n";
}

function infoList(){
    echo "\n";
    echo "Per a esborrar una tasca, es necessari posar els següents arguments: \n";
    echo "php <fitxer.php> (-a/--action) <list>\n";
    echo "EXEMPLE: php <document.php> (-a/--action) list\n";
}

function infoMarcar(){
    echo "\n";
    echo "Per a marcar una tasca com a finalitzada: \n";
    echo "Hauras de escriure la seguent estructura: \n";
    echo "php <fitxer.php> (-a/--action) <mark> (-t/--title)<id de la tasca> (-d/--description) <done>\n";
}

function add($titol, $descripcio, $conn) {

    if(trim($titol) != "" && trim($descripcio) != ""){

        $sql = "INSERT INTO events (titol, descripcio) VALUES ('$titol', '$descripcio')";
        $resultado = mysqli_query($conn, $sql);
        return $resultado;

    }else{
        infoadd();
    }
}
   

function delete($titol,$conn){

    if(trim($titol) != ""){
        $sql = "DELETE FROM events WHERE id = '$titol'";
        $resultado = mysqli_query($conn,$sql);
        return $resultado;
    }else{
        infoDelete();
    }
 
}

function listar($accio,$conn){

    if (trim($accio) != "" ){
        $sql = "SELECT * FROM events";
        $resultado = mysqli_query($conn,$sql);
        return $resultado;
    }else{
        infoList();
    }  
}


function mark($titol, $descripcio, $conn){
    if(trim($titol) != "" && trim($descripcio) != ""){
        $sql = "UPDATE events SET completada = 1 WHERE ID = '$titol' AND '$descripcio' = 'done'";
        $resultado = mysqli_query($conn, $sql); 
        return $resultado;
    }else{
        infoMarcar();
    }
}

const add = 'add';
const delete = 'delete';
const listar = 'list';
const mark = 'mark';

if (php_sapi_name() == 'cgi') {
    die('El programa nomes es pot utilitzar en CLI');
} else{

    $arguments = getopt("a:d:t:", array("action:","title:","description:"));


    if ($argc <= 7){
        
            switch ($arguments["a"] ?? $arguments ["action"]){
                case add:
                    if (($argc == 7 && (!empty($arguments["t"]) || !empty($arguments["title"])) && (!empty($arguments["d"]) || !empty($arguments["description"])))) {
                        
                        $titol = $arguments['t'] ?? $arguments['title'];
                        $descripcio = $arguments['d'] ?? $arguments['description'];
                        $resultado = add($titol, $descripcio, $conn);

                        if ($resultado){
                            echo "Tasca ingresada de forma correcta\n";
                        }else{
                            echo "Hi aparegut algun error\n";
                        }
                    }else{
                        infoadd();
                    }
                   
                    break;

                case delete:
                    if ($argc == 5 && (!empty($arguments["t"]) || !empty($arguments["title"]))){
                        
                        $titol = $arguments['t'] ?? $arguments['title'];
                        $resultado = delete($titol, $conn);

                      
                        if ($resultado) {
                            echo "S'ha esborrat correctament la tasca '$titol'\n";
                        } else {
                            echo "No s'ha pogut borrar la tasca '$titol'\n";
                        }
                    } else {
                        infoDelete();
                    }
                    break;

                    case listar:
                        if (($argc == 3 && !empty($arguments["a"])) || ($argc == 3 && !empty($arguments["action"]))) {
                            
                            $accio = $arguments['a'] ?? $arguments['action'];
                            $resultado = listar($accio,$conn);

                    
                            if ($resultado) {
                                echo "  RESULTATS TROBATS: \n";
                                echo "________________________________\n";
                                echo "\n";
                                echo "ID - TITOL - DESCRIPCIO - ESTAT\n";
                    
                                while ($fila = mysqli_fetch_assoc($resultado)) {
                                    echo $fila["id"]. " | ";
                                    echo $fila["titol"]. " | ";
                                    echo $fila["descripcio"]. " | ";
                                    echo $fila["completada"]. " | ";
                                    echo "\n";
                                }
                            } else {
                                echo "No s'ha trobat cap registre en la base de dades \n";
                            }
                            
                        } else {
                            infoList();
                        }
                    break;
                    case mark:
                        if ($argc == 7) {
                            if ((!empty($arguments["t"]) || !empty($arguments["title"])) && ($arguments["d"] == 'done' || $arguments["description"] == 'done')) {

                                $titol = $arguments['t'] ?? $arguments['title'];
                                $descripcio = $arguments['d'] ?? $arguments['description'];
                                
                                $resultado = mark($titol, $descripcio, $conn);
                                
                                if ($resultado) {
                                    echo "Tasca actualitzada de forma correcta\n";
                                } else {
                                    echo "Hi ha aparegut un error\n";
                                }
                               
                            } else {
                                infoMarcar();
                            }
                        } else {
                            infoMarcar();
                        }
                        break;
                    default:
                        infoGeneral();
            }
        
    }else{
        infoGeneral();
    }

    mysqli_close($conn);

}
?>