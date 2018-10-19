<?php

$file = fopen("http://localhost/PG2/municipios/region.txt", "r");

        //$this->conexao = new Connection();

        while (!feof($file)) {
            $line = fgets($file);
            
            echo $line;

        }
        fclose($file);

        return $regions;
?>
