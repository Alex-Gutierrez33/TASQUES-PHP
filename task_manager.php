<?php

include ("guias_task-manager.php");
require 'vendor/autoload.php';
use Symfony\Component\Yaml\Yaml;

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

    $message = getMessage();
    echo $message['messages']['insertarDades']['csv']['feedbackOK'];
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

    $message = getMessage();
    echo $message['messages']['marcarDades']['csv']['feedbackOK'];
}


function verifyExistDatabase(){

    $array = getParameSQL();

    $servername = $array['database']['host'];
    $username = $array['database']['user'];
    $password = $array['database']['password'];
    $db = $array['database']['name'];

    if ($servername == "" && $username == "" && $password == "" && $db == "") {
        return FALSE;
    }
    

    try {
        $conn = mysqli_connect($servername, $username, $password);

        $databaseName = $db;
    
    
        $sql = "SHOW DATABASES LIKE '$databaseName'";
        $resultado = mysqli_query($conn, $sql);

        if ($resultado && mysqli_num_rows($resultado) > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    
    
    } catch (mysqli_sql_exception $e) {
    }
    
    
    }

function createDatabase(){

    $array = getParameSQL();

    $servername = $array['database']['host'];
    $username = $array['database']['user'];
    $password = $array['database']['password'];
    $db = $array['database']['name'];


    try {
        $conn = mysqli_connect($servername, $username, $password);
        
        if($conn){
            
            $sql = "CREATE DATABASE $db";
            $resultado = mysqli_query($conn, $sql);
        }
            
            
        mysqli_select_db($conn, $db);
            
        $sqlCreateTable = "CREATE TABLE events(
            id INT AUTO_INCREMENT PRIMARY KEY,
            titol VARCHAR(50) NOT NULL,
            descripcio VARCHAR(100) NOT NULL,
            completada BOOLEAN DEFAULT FALSE
        );";
            
        $resultadoCreateTable = mysqli_query($conn, $sqlCreateTable);
            
        if($resultadoCreateTable){
            echo "S'ha creat la base de dades \n";
            $message = getMessage();
            echo $message['messages']['sql']['createDatabase']['feedbackOK'];
        }else{
            $message = getMessage();
            echo $message['messages']['sql']['createDatabase']['feedbackBad'];
        }
    
    } catch (mysqli_sql_exception $e) {
       
    }
        
   
        
}



function addTaskSQL($titol, $descripcio) {


    $array = getParameSQL();

    $servername = $array['database']['host'];
    $username = $array['database']['user'];
    $password = $array['database']['password'];
    $db = $array['database']['name'];


        
    try {
        $conn = mysqli_connect($servername, $username, $password,$db);

        $sql = "INSERT INTO events (titol, descripcio) VALUES ('$titol', '$descripcio')";
        $resultado = mysqli_query($conn, $sql);
        return $resultado;
    } catch (mysqli_sql_exception $e) {
        $message = getMessage();
        echo $message['messages']['insertarDades']['sql']['feedbackBad'];

    }
    


}

function delete($titol){


    

   $array = getParameSQL();

    $servername = $array['database']['host'];
    $username = $array['database']['user'];
    $password = $array['database']['password'];
    $db = $array['database']['name'];


        
    try {
        $conn = mysqli_connect($servername, $username, $password,$db);

        $sql2 = "SELECT * FROM events WHERE id = '$titol'";
        $resultado2 = mysqli_query($conn,$sql2);

        $resultado = null;

        if (mysqli_num_rows($resultado2) > 0) {
            $sql = "DELETE FROM events WHERE id = '$titol'";
            $resultado = mysqli_query($conn,$sql);      
        }else{
            $message = getMessage();
            echo $message['messages']['borrarDades']['sql']['tasknotFound'];

        }

        return $resultado;

    } catch (mysqli_sql_exception $e) {

    }


    
}

function listar(){

    $array = getParameSQL();

    $servername = $array['database']['host'];
    $username = $array['database']['user'];
    $password = $array['database']['password'];
    $db = $array['database']['name'];

    try {
        $conn = mysqli_connect($servername, $username, $password,$db);

        $sql = "SELECT * FROM events";
        $resultado = mysqli_query($conn,$sql);
        return $resultado;
    } catch (mysqli_sql_exception $e) {

    }
        
   


}

function mark($titol, $descripcio){

    $array = getParameSQL();

    $servername = $array['database']['host'];
    $username = $array['database']['user'];
    $password = $array['database']['password'];
    $db = $array['database']['name'];

  
        

    try {
        $conn = mysqli_connect($servername, $username, $password,$db);

        $sql2 = "SELECT * FROM events WHERE id = '$titol'";
        $resultado2 = mysqli_query($conn,$sql2);

        $resultado = null;

        if (mysqli_num_rows($resultado2) > 0) {
            $sql = "UPDATE events SET completada = 1 WHERE ID = '$titol' AND '$descripcio' = 'done'";
            $resultado = mysqli_query($conn, $sql);       
        }else{
            echo "No s'ha trobat la tasca a marcar\n";
            $message = getMessage();
            echo $message['messages']['marcarDades']['sql']['tasknotFound'];

        }

        return $resultado;

    } catch (mysqli_sql_exception $e) {

    }

    

}


function showTaskCSV(){

    $pathCSV = pathCSV();
    $file = fopen($pathCSV, 'r');


   
    echo "Error no s'ha pogut obrir el arxiu\n";
  
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
    $sql2 = "SELECT * FROM events WHERE id = '$titol'";

    $result2 = $db->querySingle($sql2);

    if ($result2) {
        $result = $db->exec($sql);
        return $result;

    }else{
        $message = getMessage();
        echo $message['messages']['borrarDades']['sqlite']['tasknotFound'];
    }

   
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
    $sql2 = "SELECT * FROM events WHERE id = '$titol'";

    $result = $db->exec($sql);
    $result2 = $db->querySingle($sql2);

   
    if ($result2) {
        $result = $db->exec($sql);
        return $result;

    }else{

       $message = getMessage();
        echo $message['messages']['marcarDades']['sqlite']['feedbackBad'];
        return $result2;
    }

}

function getMessage(){
    
    $pathMessage = 'messages.yaml';

    $message = Yaml::parseFile($pathMessage);

    return $message;

}

function pathCFGyaml(){
    $homedir = getenv('HOME');
    $pathCFG = $homedir. DIRECTORY_SEPARATOR.'.config'.DIRECTORY_SEPARATOR.'task-manager-config.yaml';
    return $pathCFG;

}

function createYAML($ruta) {

    $archivo = fopen($ruta, 'w');        

    $configYAML = array(
        'method' => '',
        'database' => array(
            'host' => '',
            'name' => '',
            'user' => '',
            'password' => '',
        )
    );

    $contenidoYAML = yaml_emit($configYAML);

    file_put_contents($ruta, $contenidoYAML);
 
}

function getmethod(){
    $pathCFG = pathCFGyaml();

    $message = Yaml::parseFile($pathCFG);

    return $message['method'];
}


function putMethod($respuesta){

    $pathCFG = pathCFGyaml();

    $content = file_get_contents($pathCFG);
    $array = Yaml::parse($content);

    $array['method'] = $respuesta;

    $contenidoYAML = yaml_emit($array);

    file_put_contents($pathCFG, $contenidoYAML);


}

function askConfigSQL(){

    $pathCFG = pathCFGyaml();

    do {
        echo "Indica la ip on tens al base de dades: ";
        $host = trim(fgets(STDIN));            
    } while (empty($host));

    do {
        echo "Indica el nom de la base de dades: ";
        $dbname = trim(fgets(STDIN));            
    } while (empty($dbname));
        

    do {
        echo "Indica el nom del usuari:  ";
        $username = trim(fgets(STDIN));            
    } while (empty($username));
        
    do {
        echo "Indica la contrasenya del usuari: ";
        $password = trim(fgets(STDIN));            
    } while (empty($password));


    $content = file_get_contents($pathCFG);

    $array = Yaml::parse($content);

    $array['database']['host'] = $host;
    $array['database']['name'] = $dbname;
    $array['database']['user'] = $username;
    $array['database']['password'] = $password;

    $contenidoYAML = yaml_emit($array);

    file_put_contents($pathCFG, $contenidoYAML);

    }

    function getParameSQL(){
        $pathCFG = pathCFGyaml();
    
        $message = Yaml::parseFile($pathCFG);
    
       return $message;
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
                    
                    $pathCFGyaml = pathCFGyaml();

                    if (file_exists($pathCFGyaml)) {
                        $respuesta = getmethod();
                    }else{
                        createYAML($pathCFGyaml);
                        $respuesta = verificarArchivos();
                        putMethod($respuesta);
                        $respuesta = getmethod();


                        if ($respuesta == 'SQL') {
                            askConfigSQL();
                        }

                    }


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
                                
                                askConfigSQL();
                                createDatabase();  
                                $accio = addTaskSQL($titol,$descripcio);

                                if($accio){
                                    $message = getMessage();
                                    echo $message['messages']['insertarDades']['sql']['feedbackOK'];
                                }else{
                                    $message = getMessage();
                                    echo $message['messages']['insertarDades']['sql']['feedbackBad'];
                                }

                            }else{
                                
                                //askConfigSQL();
                                $accio = addTaskSQL($titol,$descripcio);

                                if($accio){
                                    $message = getMessage();
                                    echo $message['messages']['insertarDades']['sql']['feedbackOK'];
                                }else{
                                    $message = getMessage();
                                    echo $message['messages']['insertarDades']['sql']['feedbackOK'];
                                }
                            }
                            break;
                        case 'SQLITE':

                            $verify = verifiySQLITE();

                            $titol = $arguments['t'] ?? $arguments['title'];
                            $descripcio = $arguments['d'] ?? $arguments['description'];

                            if($verify){
                                insertData($titol,$descripcio);
                                $message = getMessage();
                                echo $message['messages']['insertarDades']['sqlite']['feedbackOK'];
                            }else{
                                createSQLITE();
                                insertData($titol,$descripcio);
                                $message = getMessage();
                                echo $message['messages']['insertarDades']['sqlite']['feedbackOK'];
                            }
                            break;

                    }

                }else{
                    $message = getMessage();
                    echo $message['messages']['infoadd'];
                }
                break;

            case delete:
                $contador = 0;
                if(($argc == 5 && ((!empty($arguments["t"]) && trim($arguments["t"]) != "") || (!empty($arguments["title"]) && trim($arguments["title"]) != "")) )){
        
                    

                    $pathCFGyaml = pathCFGyaml();

                    if (file_exists($pathCFGyaml)) {
                        $method = getmethod();

                        switch ($method) {
                            case 'CSV':
                                $titol = $arguments['t'] ?? $arguments['title'];
                                deleteTaskCSV($titol);
                                $message = getMessage();
                                echo $message['messages']['borrarDades']['csv']['feedbackOK'];
                                break;
    
                            case 'SQL':
                                $titol = $arguments['t'] ?? $arguments['title'];
                                $accio = delete($titol);
                                if ($accio) {
        
                                    $message = getMessage();
                                    echo $message['messages']['borrarDades']['sql']['feedbackOK'];
                                    $contador++;
                                }else{
                                    $contador++;
                                }
                                break;
    
                            case 'SQLITE':
                                $result = deleteData($titol);
                                $contador++;
        
        
                                if ($result) {
                                    $message = getMessage();
                                    echo $message['messages']['borrarDades']['sqlite']['feedbackOK'];
                                    
                                }else{
                                    $contador++;
                                }
                                break;      
                        } 
                    }else{
                        $message = getMessage();
                        echo $message['messages']['errorEmmagatzematge'];
                    }
                        
   
                }else{
                    $message = getMessage();
                    echo $message['messages']['infodelete'];
                }
            break;

        case listar:
            $contador = 0;
            if(($argc == 3) && ((!empty($arguments['a']) && trim($arguments['a']) != '') || (!empty($arguments['action']) && trim($arguments['action']) != ''))) {
                
               
                $pathCFGyaml = pathCFGyaml();

                if (file_exists($pathCFGyaml)) {
                    $method = getmethod();

                    switch ($method) {
                        case 'CSV':
                            showTaskCSV();
                            $contador++;
                            break;
    
                        case 'SQL':
                            $lista = listar();
        
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
                                $message = getMessage();
                                echo $message['messages']['listarDades']['sql']['feedbackOK'];
        
                            }
                            break;
    
                        case 'SQLITE':
                            $contador++;
                            listData();
                            break;
                    }

                }else{
                    $message = getMessage();
                    echo $message['messages']['errorEmmagatzematge'];
                }
                 

            }else{
                $message = getMessage();
                echo $message['messages']['infolist'];
            }
            break;

        
            case mark:
                $contador = 0;
    
                if(($argc == 7 && ((!empty($arguments["t"]) && trim($arguments["t"]) != "") || (!empty($arguments["title"]) && trim($arguments["title"]) != "")) && ((!empty($arguments["d"])&& trim($arguments["d"]) == "done") || (!empty($arguments["description"]) && trim($arguments["description"]) == "done")))){
                    

                    $titol = $arguments['t'] ?? $arguments['title'];
                    $descripcio = $arguments['d'] ?? $arguments['description'];
                    

                    $pathCFGyaml = pathCFGyaml();

                    if (file_exists($pathCFGyaml)) {
                        $method = getmethod();

                        switch ($method) {
                            case 'CSV':
                                markCSV($titol);
                                $contador++;
                                break;
    
                            case 'SQL':
                                $resultadoMarcar = mark($titol,$descripcio);
                            
                                if($resultadoMarcar){
                                    $message = getMessage();
                                    echo $message['messages']['marcarDades']['sqlite'];['feedbackOK'];
                                    $contador++;
                                }else{
                                    $contador++;
                                }
                                break;
    
                            case 'SQLITE':
                                $result = markTASKSQLITE($titol,$descripcio);
    
                                if($result){
                                    $message = getMessage();
                                    echo $message['messages']['marcarDades']['sqlite']['feedbackOK'];
                                    $contador++;
                                }else{
                                    $contador++;
    
                                }
                                break;
                            
                            
                        }
                    }else{
                        $message = getMessage();
                        echo $message['messages']['errorEmmagatzematge'];
                    }

                   
                }else{
                    $message = getMessage();
                    echo $message['messages']['infomarcar'];
                }
                break;

           
        default:
            $message = getMessage();
            echo $message['messages']['infogeneral'];
        }
    }else{
        $message = getMessage();
        echo $message['messages']['infogeneral'];
    }

}

?>