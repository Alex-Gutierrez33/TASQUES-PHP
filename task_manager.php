<?php

include ("guias_task-manager.php");

function pathCSV(){
    $homedir = getenv('HOME');
    $pathCSV = $homedir. DIRECTORY_SEPARATOR.'.config'.DIRECTORY_SEPARATOR.'task-manager.csv';
    return $pathCSV;
}


function pathSQLITE(){
    $homedir = getenv('HOME');
    $pathSQLITE = $homedir. DIRECTORY_SEPARATOR.'.config'.DIRECTORY_SEPARATOR.'task-manager.db';
    return $pathSQLITE;

}


function verifiySQLITE(){
    $pathSQLITE = pathSQLITE();

    if (file_exists($pathSQLITE)){
        return TRUE;
    }else{
        return FALSE;
    }
}
function verificarArchivos (){

    $pathCSV = pathCSV();
    $pathSQLITE = pathSQLITE();
    $contador = 0;
    $respuesta = "";
   

    $verify = verifyExistDatabase();

    if($verify == TRUE){
        $contador ++;
        $respuesta = "SQL";
    }

 

    if (file_exists($pathSQLITE)){
        $contador ++;
        $respuesta = "SQLITE";
    } 
    
    
    if (file_exists($pathCSV)){
        $contador ++;
        $respuesta = "CSV";
    }

    echo "\n";
  
    if ($contador == 0){

        while(TRUE){
            echo "Indica en quin medi vols emmagatzemar les dades (CSV/SQLITE/SQL): ";
            $respuesta = trim(fgets(STDIN));

            if($respuesta == "CSV"){
                break;
            }

            if($respuesta == "SQL"){
                break;
            }

            if($respuesta == "SQLITE"){
                break;
              }
        }
    }
    return $respuesta;

}


function createCSV($tasca){

    $pathCSV = pathCSV();

    $file = fopen($pathCSV, 'a');
    fputcsv($file, $tasca);

    fclose($file);

    echo "Tasca ingresada de forma correcta". "\n";   
}

function deleteTaskCSV($titol){

    $pathCSV = pathCSV();

    $file = fopen($pathCSV, 'r');

    $lista = [];
    
    while(($row = fgetcsv($file)) != false){
        if ($row[0] != $titol) {
            $lista[] = $row;
        }
    }
    
    fclose($file);
    
    $file = fopen($pathCSV, 'w');
    
    foreach ($lista as $fila) {
        fputcsv($file, $fila);
    }
    
    fclose($file);

}


function markCSV($titol){

    $pathCSV = pathCSV();
    $filaNueva = array();

    $file = fopen($pathCSV, "r");
    while(($row = fgetcsv($file)) != false){
        if($row[0] == $titol){
            $row[3] = 'SI';
        }

        array_push($filaNueva, $row);
    }

    $file = fopen($pathCSV, 'w');

    foreach($filaNueva as $fila){
        fputcsv($file, $fila);

    }

    fclose($file);

    echo "L'estat de la tasca s'ha modificat \n";
}


function verifyExistDatabase(){
    
    $servername = "localhost";
    $username = "alex";
    $password = "Alex2310";
    
    $conn = mysqli_connect($servername, $username, $password);
    
    
    $databaseName = "activitat";
    
    
    $sql = "SHOW DATABASES LIKE '$databaseName'";
    $resultado = mysqli_query($conn, $sql);
    
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        return TRUE;
    } else {
        return FALSE;
    }

    
    }

function createDatabase(){

    $servername = "localhost";
    $username = "alex";
    $password = "Alex2310";
        
    $conn = mysqli_connect($servername, $username, $password);
        
    if($conn){
        
        $sql = "CREATE DATABASE activitat";
        $resultado = mysqli_query($conn, $sql);
    }
        
        
    mysqli_select_db($conn, "activitat");
        
    $sqlCreateTable = "CREATE TABLE events(
        id INT AUTO_INCREMENT PRIMARY KEY,
        titol VARCHAR(50) NOT NULL,
        descripcio VARCHAR(100) NOT NULL,
        completada BOOLEAN DEFAULT FALSE
    );";
        
    $resultadoCreateTable = mysqli_query($conn, $sqlCreateTable);
        
    if($resultadoCreateTable){
        echo "S'ha creat la base de dades \n";
    }else{
        echo "Ha aparegut un error \n";
    }

        
}


function addTaskSQL($titol, $descripcio) {

    $servername = "localhost";
    $username = "alex";
    $password = "Alex2310";
    $db = "activitat";

    $conn = mysqli_connect($servername, $username, $password,$db);

    $sql = "INSERT INTO events (titol, descripcio) VALUES ('$titol', '$descripcio')";
    $resultado = mysqli_query($conn, $sql);
    return $resultado;


}

function delete($titol){

    $servername = "localhost";
    $username = "alex";
    $password = "Alex2310";
    $db = "activitat";

    $conn = mysqli_connect($servername, $username, $password,$db);

    $sql = "DELETE FROM events WHERE id = '$titol'";
    $resultado = mysqli_query($conn,$sql);
    return $resultado;


 
}

function listar($accio){

    $servername = "localhost";
    $username = "alex";
    $password = "Alex2310";
    $db = "activitat";

    $conn = mysqli_connect($servername, $username, $password,$db);

    $sql = "SELECT * FROM events";
    $resultado = mysqli_query($conn,$sql);
    return $resultado;


}

function mark($titol, $descripcio){

    $servername = "localhost";
    $username = "alex";
    $password = "Alex2310";
    $db = "activitat";

    $conn = mysqli_connect($servername, $username, $password,$db);
    
    $sql = "UPDATE events SET completada = 1 WHERE ID = '$titol' AND '$descripcio' = 'done'";
    $resultado = mysqli_query($conn, $sql); 
    return $resultado;

}


function showTaskCSV(){

    $pathCSV = pathCSV();


    $file = fopen($pathCSV, 'r');

    echo "TASQUES DISPONIBLES: " . "\n";
    echo "=====================" . "\n";

    while(($row = fgetcsv($file)) != false){
        echo "id:" . $row[0] . " ". "titol:" . $row[1] . " " . "descripcio:" . $row[2]." ". "completada:" . $row[3]."\n";
    
    }

    fclose($file);

}

function createSQLITE(){
    $fileSQLITE = pathSQLITE();
    $db = new SQLite3($fileSQLITE);

    $sql = "CREATE TABLE IF NOT EXISTS events (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        titol VARCHAR(50) NOT NULL,
        descripcio VARCHAR(100) NOT NULL,
        completada BOOLEAN DEFAULT FALSE
    )";

    $result = $db->exec($sql);
}

function insertData($titol, $descripcio){
    $fileSQLITE = pathSQLITE();
    $db = new SQLite3($fileSQLITE);

    $sql = "INSERT INTO events (titol, descripcio) VALUES ('$titol', '$descripcio')";

    $result = $db->exec($sql);
}

function deleteData($titol){
    $fileSQLITE = pathSQLITE();
    $db = new SQLite3($fileSQLITE);

    $sql = "DELETE FROM events WHERE id = '$titol'";

    $result = $db->exec($sql);
}

function listData(){
    $fileSQLITE = pathSQLITE();
    $db = new SQLite3($fileSQLITE);

    $sql = "SELECT * FROM events";

    $result = $db->query($sql);

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        echo "ID: " . $row['id'] . "\n";
        echo "Títol: " . $row['titol'] . "\n";
        echo "Descripció: " . $row['descripcio'] . "\n";
        echo "Estat: " . $row['completada'] . "\n";
        echo "\n";
    }
}

function markTASKSQLITE($titol, $descripcio){
    $fileSQLITE = pathSQLITE();
    $db = new SQLite3($fileSQLITE);

    $sql = "UPDATE events SET completada = 1 WHERE ID = '$titol' AND '$descripcio' = 'done'";

    $result = $db->exec($sql);
    
    return $result;


}



const add = 'add';
const delete = 'delete';
const listar = 'list';
const mark = 'mark';

if (php_sapi_name() == 'cgi') {
    die('El programa nomes es pot utilitzar en CLI');
}else{

    $arguments = getopt("a:d:t:", array("action:","title:","description:"));

    if ($argc <= 7){
        switch($arguments['a'] ?? $arguments['action']){
            case add:
                if(($argc == 7 && ((!empty($arguments["t"]) && trim($arguments["t"]) != "") || (!empty($arguments["title"]) && trim($arguments["title"]) != "")) && ((!empty($arguments["d"])&& trim($arguments["d"]) != "") || (!empty($arguments["description"]) && trim($arguments["description"]) != "")))){
                    
                    $respuesta = verificarArchivos();

                    switch($respuesta){
                        case 'CSV':

                            $id = rand(1, 999999999);
                            $titol = $arguments['t'] ?? $arguments['title'];
                            $descripcio = $arguments['d'] ?? $arguments['description'];

                            $tasca = array("id" => $id, "titol" => $titol, "descripcio" => $descripcio, "completada" => "NO");

                            createCSV($tasca);
                            break;

                        case 'SQL':
                            $result = verifyExistDatabase();

                            $titol = $arguments['t'] ?? $arguments['title'];
                            $descripcio = $arguments['d'] ?? $arguments['description'];

                            if($result != TRUE){

                                createDatabase();  
                                $accio = addTaskSQL($titol,$descripcio);

                                if($accio){
                                    echo "Tasca ingresada de forma correcta \n";
                                }else{
                                    echo "No s'ha pogut ingresar la tasca \n";
                                }

                            }else{
                                
                                $accio = addTaskSQL($titol,$descripcio);

                                if($accio){
                                    echo "Tasca ingresada de forma correcta \n";
                                }else{
                                    echo "No s'ha pogut ingresar la tasca \n";
                                }
                            }
                            break;
                        case 'SQLITE':

                            $verify = verifiySQLITE();

                            $titol = $arguments['t'] ?? $arguments['title'];
                            $descripcio = $arguments['d'] ?? $arguments['description'];

                            if($verify){
                                insertData($titol,$descripcio);
                                echo "S'han insertat les dades de forma correcta \n";

                            }else{
                                createSQLITE();
                                insertData($titol,$descripcio);
                                echo "S'ha creat la base de dades SQLITE \n";
                            }
                            break;

                    }

                }else{
                    infoadd();
                }
                break;

            case delete:
                $contador = 0;
                if(($argc == 5 && ((!empty($arguments["t"]) && trim($arguments["t"]) != "") || (!empty($arguments["title"]) && trim($arguments["title"]) != "")) )){
        
                    $pathCSV = pathCSV();
                    $result = verifyExistDatabase();
                    $verify = verifiySQLITE();


                    $titol = $arguments['t'] ?? $arguments['title'];

                    if (file_exists($pathCSV)){
                        deleteTaskCSV($titol);
                        echo "Tasca esborrada de forma correcta \n";
                        $contador++;
                    }

                    if($result){
                        $accio = delete($titol);

                        if ($accio) {
                            echo "S'ha esborrat correctament la tasca '$titol'\n";
                            $contador++;
                        }
 
                    }

                    if($verify){
                        echo "S'ha esborrat correctament la tasca '$titol'\n";
                        deleteData($titol);
                        $contador++;
                    }

                    if($contador == 0){
                        echo "No es pot executar aquesta acció !! NO EXISTEIX UN MEDI D'EMMAGATZEMATGE \n";
                    }
   
                }else{
                    infoDelete();
                }
            break;

        case listar:
            $contador = 0;
            if(($argc == 3) && ((!empty($arguments['a']) && trim($arguments['a']) != '') || (!empty($arguments['action']) && trim($arguments['action']) != ''))) {
                
                $pathCSV = pathCSV();
                $result = verifyExistDatabase();
                $verify = verifiySQLITE();



                if (file_exists($pathCSV)){
                    showTaskCSV();
                    $contador++;
                }

                if($result){
                    $accio = $arguments['a'] ?? $arguments['action'];
                    $lista = listar($accio);

                    if($lista){
                        echo "  RESULTATS TROBATS: \n";
                        echo "____________________________\n";
                        echo "\n";
                        echo "ID - TITOL - DESCRIPCIO - ESTAT\n";

                        while ($fila = mysqli_fetch_assoc($lista)) {
                            echo $fila["id"]. " | ";
                            echo $fila["titol"]. " | ";
                            echo $fila["descripcio"]. " | ";
                            echo $fila["completada"]. " | ";
                            echo "\n";
                        }
                        $contador++;
                    }else{
                        echo "No s'ha trobat cap dada en la base de dades \n";
                    }

                }

                if ($verify) {
                    $contador++;
                    listData();                
                }

                if($contador == 0){
                    echo "No es pot executar aquesta acció !! NO EXISTEIX UN MEDI D'EMMAGATZEMATGE \n";
                }

            }else{
                infoList();
            }
            break;

        
            case mark:
                $contador = 0;
    
                if(($argc == 7 && ((!empty($arguments["t"]) && trim($arguments["t"]) != "") || (!empty($arguments["title"]) && trim($arguments["title"]) != "")) && ((!empty($arguments["d"])&& trim($arguments["d"]) != "") || (!empty($arguments["description"]) && trim($arguments["description"]) == "done")))){
                    
                    $pathCSV = pathCSV();
                    $result = verifyExistDatabase();
                    $verify = verifiySQLITE();


                    $titol = $arguments['t'] ?? $arguments['title'];
                    $descripcio = $arguments['d'] ?? $arguments['description'];


                    if (file_exists($pathCSV)){
                        markCSV($titol);
                        $contador++;
                    }

                    if($result){
                        $resultadoMarcar = mark($titol,$descripcio);
                        
                        if($resultadoMarcar){
                            echo "S'ha marcat la tasca seleccionada \n";
                            $contador++;
                        }
                    }

                    if ($verify) {
                        $result = markTASKSQLITE($titol,$descripcio);

                        if($result){
                            echo "S'ha marcat la tasca '$titol' \n";
                            $contador++;
                        }else{
                            echo "NO S'ha marcat la tasca '$titol' \n";

                        }


                    }

                    if($contador == 0){
                        echo "No es pot executar aquesta acció !! NO EXISTEIX UN MEDI D'EMMAGATZEMATGE \n";
                    }
                }else{
                    infoMarcar();
                }
                break;

           
        default:
            infoGeneral();
        }
    }else{
        infoGeneral();
    }

}

?>