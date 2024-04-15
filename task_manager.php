
<?php

include 'conn.php';

function infoGeneral(){
    echo "\n";
    echo "***MANUAL D'ÚS***\n";
    echo "\n";
    echo "La sintaxis correcta es: php <fitxer.php> <tasca> <titol> <contingut>\n";
    echo "\n";
    echo "Tasques disponibles:\n";
    echo "-add: Permet afegir una tasca\n";
    echo "-list: Mostra per pantalla totes les tasques disponibles\n";
    echo "-mark: Pots modificar una tasca\n";
    echo "-delete: Tens la opcio de eliminar qualsevol tasca\n";

}

function infoadd(){
    echo "\n";
    echo "Per a afegir una tasca, es necessari posar els següents arguments: \n";
    echo "php <fitxer> <add> <titol de la tasca> <contingut>\n";
    echo "\n";
    echo "NOMES ES NECESSARI AFEGIR 3 ARGUMENTS !!\n";
}

function infoDelete(){
    echo "\n";
    echo "Per a esborrar una tasca, es necessari posar els següents arguments: \n";
    echo "php <fitxer> <delete> <id de la tasca>\n";
    echo "\n";
    echo "NOMES ES NECESSARI AFEGIR 2 ARGUMENTS !!\n";
}

function infoList(){
    echo "\n";
    echo "Per a llistar les tasques disponibles, nomes es necessari un argument: ";
    echo "\n";
    echo "EXEMPLE: php <document.php> list\n";
}

function infoMarcar(){
    echo "\n";
    echo "Per a marcar una tasca com a finalitzada: \n";
    echo "Hauras de escriure la seguent estructura: \n";
    echo "php <document.php> <id_tasca> <done>\n";
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
   

function delete($accio,$titol,$conn){

    if(trim($accio) != "" && trim($titol) != ""){
        $sql = "DELETE FROM events WHERE ID = '$titol'";
        $resultado = mysqli_query($conn,$sql);
        return $resultado;
    }else{
        infoDelete();
    }
 
}


function listar($accio,$conn){

    if (trim($accio) != ""){
        $sql = "SELECT * FROM events";
        $resultado = mysqli_query($conn,$sql);
        return $resultado;
    }else{
        infoList();
    }  
}


function mark($accio,$titol,$descripcio,$conn){
    if(trim($accio) != "" && trim($titol) != "" && trim($descripcio) !=""){
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

    if ($argc <= 4){


            switch ($argv[1]){
                case add:
                    if ($argc == 4 && !empty($argv[1]) && !empty($argv[2]) && !empty($argv[3]))   {

                        $resultado = add($argv[2],$argv[3],$conn);
    
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
                    if ($argc == 3 && !empty($argv[1]) && !empty($argv[2])){
                        
                        $resultado = delete($argv[1],$argv[2],$conn);

                        if ($resultado){
                            echo "S'ha esborrat correctament la tasca '$argv[2]'\n";
                        }else{
                            echo "No s'ha pogut borrar la tasca '$argv[2]'\n";
                        }
                    }else{
                        infoDelete();
                    }
                    break;
                case listar:
                    if ($argc == 2 && !empty($argv[1])){
                        
                        $resultado = listar($argv[1],$conn);
                        if ($resultado){
                            echo "  RESULTATS TROBATS: \n";
                            echo "______________________________________________________\n";
                            echo "\n";
                            echo "ID  TITOL      DESCRIPCIO    ESTAT\n";
    
                            while ($fila = mysqli_fetch_assoc($resultado)) {
                                echo $fila["id"]. " | ";
                                echo $fila["titol"]. " | ";
                                echo $fila["descripcio"]. " | ";
                                echo $fila["completada"]. " | ";
                                echo "\n";
                            }
                        }else{
                            echo "No s'ha trobat cap registre en la base de dades \n";
                        }
        
                    }else{
                        infoList();
                    }
                    break;
                case mark:
                    if ($argc == 4){
                        if ($argv[3] == 'done' && !empty($argv[1]) && !empty($argv[2])){
                            
                            $resultado = mark($argv[1],$argv[2],$argv[3],$conn);   
                            if ($resultado) {
                                echo "La tasca '$argv[2]' s'ha marcat com a completada\n";
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
