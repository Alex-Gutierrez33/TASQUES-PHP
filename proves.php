<?php
    require_once 'vendor/autoload.php'; // Cargar el autoloader de Symfony

    use Symfony\Component\Yaml\Yaml;

    function pathCFG(){
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
        $pathCFG = pathCFG();
    
        $message = Yaml::parseFile($pathCFG);
    
        return $message['method'];
    }

    function getParameSQL(){
        $pathCFG = pathCFG();
    
        $message = Yaml::parseFile($pathCFG);
    
       return $message;
    }

    function putMethod($respuesta){

        $pathCFG = pathCFG();

        $content = file_get_contents($pathCFG);
        $array = Yaml::parse($content);

        $array['method'] = $respuesta;

        $contenidoYAML = yaml_emit($array);

        file_put_contents($pathCFG, $contenidoYAML);


    }

    function askConfigSQL(){

        $pathCFG = pathCFG();
   
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

    $pathCFG = pathCFG();
    createYAML($pathCFG);
    askConfigSQL();
    
   

    


   

?>