<?php

namespace app;

class Views{

    public function getView($controlador, $vista, $data="")
    {
        $controlador = get_class($controlador);
        if ($controlador == "Home") {
            $vista = "Views/".$vista.".php";
            require $vista;
            return;
        }
        $vista = "Views/".$controlador."/".$vista.".php";
        require $vista;
    }
}


?>