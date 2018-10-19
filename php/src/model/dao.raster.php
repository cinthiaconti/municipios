<?php

include_once('dao.conexao.php');
include_once("entity.raster.php");

class RasterDAO {

    private static $instance;

    public static function getInstance() {
        if (!isset(self::$instance))
            self::$instance = new RasterDAO();

        return self::$instance;
    }

    public function listObjects() {

        $this->conexao = new Connection();

        $sql = "SELECT block_size,dx,dy,weight FROM raster";

        $result = $this->conexao->executeSQL($sql);

        while ($row = pg_fetch_row($result)) {

            $raster = new Raster();

            $raster->blockSize = log(2 ^ $row[0]);
            $raster->dx = $row[1];
            $raster->dy = $row[2];
            $raster->weight = $row[3];

            $rasters[] = $raster;
        }

        return $rasters;
    }

    public function insertRasterBd() {

        $file = fopen("http://localhost/PG2/municipios/raster.txt", "r");

        $this->conexao = new Connection();

        while (!feof($file)) {
            $line = fgets($file);

            if (strrpos($line, "(1 4")) {

                $attrs = explode(" ", substr(trim($line), 1));

                $raster = new Raster();

                $raster->id = $attrs[0];
                $raster->type = $attrs[1];
                $raster->minX = $attrs[2];
                $raster->minY = $attrs[3];
                $raster->maxX = $attrs[4];
                $raster->maxY = $attrs[5];
                $raster->blockSize = pow(2, $attrs[6]);
                $raster->dx = $attrs[7];
                $raster->dy = $attrs[8];
                $raster->weight = substr(trim(fgets($file)), 1, -3);

                $rasters[] = $raster;

                $sql = "INSERT INTO raster (id,type,min_x, min_y, max_x, max_y, block_size, dx, dy,weight)
                    values (" . $raster->id . "," . $raster->type . "," . $raster->minX . "," . $raster->minY . "," . $raster->maxX . "," . $raster->maxY . "," . $raster->blockSize . "," . $raster->dx . "," . $raster->dy . ",'" . $raster->weight . "');";

                $result = $this->conexao->executeSQL($sql);
            }
        }
        fclose($file);

        return $rasters;
    }

    public function insertRegionBd() {

        $file = fopen("http://localhost/PG2/municipios/region.txt", "r");

        $this->conexao = new Connection();

        while (!feof($file)) {
            $line = fgets($file);

            if (strrpos($line, "(1 4")) {

                $attrs = explode(" ", substr(trim($line), 1));

                $region = new Region();

                $region->id = $attrs[0];
                $region->type = $attrs[1];
                $region->minX = $attrs[2];
                $region->minY = $attrs[3];
                $region->maxX = $attrs[4];
                $region->maxY = $attrs[5];
                $region->blockSize = pow(2, $attrs[6]);
                $region->dx = $attrs[7];
                $region->dy = $attrs[8];
                $region->weight = substr(trim(fgets($file)), 1, -3);

                $regions[] = $region;

                $sql = "INSERT INTO region (id,type,min_x, min_y, max_x, max_y, block_size, dx, dy,weight)
                    values (" . $region->id . "," . $region->type . "," . $region->minX . "," . $region->minY . "," . $region->maxX . "," . $region->maxY . "," . $region->blockSize . "," . $region->dx . "," . $region->dy . ",'" . $region->weight . "');";

                $result = $this->conexao->executeSQL($sql);
            }
        }
        fclose($file);

        return $regions;
    }

}

?>
