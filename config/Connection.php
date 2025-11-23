<?php

namespace Config; //espacio de nombres para la clase de conexion a la base de datos

// clase que se encargara de la conexion a la base de datos
class Connection {

    protected $conn; //atributo donde guardaremos la conexion a la base de datos
    private $configuracion = [ // atributo donde guardaremos la configuracion de nuestra BD a utilizar
        "driver" => "mysql" ,        //motor de base de datos
        "host" => "localhost" ,      //el host  que aloja la BD en local
        "database" => "biblioteca" ,      //el nombre de la base de datos a donde buscamos conectarnos
        "port" => "3306" ,           //puerto de entrada o conexion a la BD, para XAMPP el por defecto 3306
        "username" => "root" ,       //usuario que administra la base de datos de interes
        "password"=> "" ,            //contraseña de acceso del usuario al gestor
        "charset" =>"utf8mb4"      /*codificacion de caracteres que se utilizara en
                                    las transacciones/consultas con las BD*/
        ] ;

        //constructor de la clase
    public function __construct() {
    }

    public function connect() { //metodo que se encargara de crear la conexion a la base de datos

        try {
            $controller = $this -> configuracion ["driver"];
            $server = $this -> configuracion ["host"];
            $database = $this -> configuracion ["database"];
            $port = $this -> configuracion ["port"];
            $username = $this -> configuracion ["username"];
            $password = $this -> configuracion ["password"];
            $charset = $this -> configuracion ["charset"];

            $dns = "{$controller}:host={$server};port={$port};dbname={$database};charset={$charset}"; //creamos la cadena de conexion a la base de datos

            $this -> conn = new \PDO($dns, $username, $password); //creamos la conexion a la base de datos

            echo "Conectado a la base de datos {$database} en {$server} con el usuario {$username} <br>"; //imprimimos un mensaje de exito en la conexion a la base de datos

            return $this -> conn; //retornamos la conexion a la base de datos

        } catch (\Exception $e) {
            echo "Error de conexión: " . $e ->getMessage(); //en caso de error se imprime el mensaje de error
        }
    }
}


?>