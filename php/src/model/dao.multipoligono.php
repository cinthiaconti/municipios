<?php

include_once('dao.conexao.php');
include_once('entity.ponto.php');
include_once('entity.poligono.php');
include_once('entity.multipoligono.php');
include_once('entity.extensao.php');

class MultiPoligonoDAO {

    private static $instancia;
    private $canvasW = 500;
    private $canvasH = 469.4; /* Brasil */

    //private $canvasH = 247; /*Translação*/
    //private $canvasH = 477; /*Rotacao*/

    public static function getInstancia() {
        if (!isset(self::$instancia))
            self::$instancia = new MultiPoligonoDAO();

        return self::$instancia;
    }

    public function obterDadosEspaciais($filtro) {

        $sql = "SELECT ST_AsText(g_map) as wkt
            FROM d_geometry
            WHERE g_region='" . $filtro . "'
            ORDER BY sky_geometry";

        $this->conexao = new Connection();
        $result = $this->conexao->executeSQL($sql);

        $extensao = $this->getExtensao(null);

        while ($row = pg_fetch_row($result)) {
            $multipoligono = new MultiPoligono();
            $poligonos = $this->converterWKTtoPoligonos($row[0], $extensao);
            $multipoligono->poligonos = $poligonos;
            $listamultipoligonos[] = $multipoligono;
        }
        return $listamultipoligonos;
    }

    public function marcarPonto($ponto, $extensao) {

        $this->conexao = new Connection();

        if ($extensao == null) {
            $extensao = $this->getExtensao(null);
        }
        $result = $this->findByPonto($ponto, $extensao);

        while ($row = pg_fetch_object($result)) {
            $poligonos = $this->converterWKTtoPoligonos($row->wkt, $extensao);
            $multipoligono->poligonos = $poligonos;
            $listamultipoligonos[] = $multipoligono;
        }
        return $listamultipoligonos;
    }

    public function zoomPonto($ponto, $extensao) {

        $this->conexao = new Connection();
        
        if ($extensao == null) {
            $extensao = $this->getExtensao(null);
        }
        $result = $this->findByPonto($ponto, $extensao);

        while ($row = pg_fetch_object($result)) {
            $extensao = $this->getExtensao($row->id);
            $poligonos = $this->converterWKTtoPoligonos($row->wkt, $extensao);
            $multipoligono->poligonos = $poligonos;
            $listamultipoligonos[] = $multipoligono;
        }
        return $listamultipoligonos;
    }

    public function zoomSelecao($zoomExtensao, $extensao) {

        $this->conexao = new Connection();
        $extensao = $this->convertExtensao($zoomExtensao, $extensao);
        $listamultipoligonos[] = $extensao;
        $result = $this->findByExtensao($extensao);

        $max = new Ponto();
        $max->x = $extensao->pontoMax->x;
        $max->y = $extensao->pontoMin->y;

        $min = new Ponto();
        $min->x = $extensao->pontoMin->x;
        $min->y = $extensao->pontoMax->y;

        $extensaoFixed = new Extensao();
        $extensaoFixed->pontoMax = $max;
        $extensaoFixed->pontoMin = $min;

        while ($row = pg_fetch_object($result)) {

            $poligonos = $this->converterWKTtoPoligonos($row->wkt, $extensaoFixed);

            $multipoligono = new MultiPoligono();
            $multipoligono->poligonos = $poligonos;
            $listamultipoligonos[] = $multipoligono;
        }
        return $listamultipoligonos;
    }

    private function findByPonto($ponto, $extensao) {

        $ponto = $this->convertCanvasPonto($ponto, $extensao);

        $sPonto = 'POINT(' . $ponto->x . ' ' . $ponto->y . ')';

        $sql = "SELECT sky_geometry as id, ST_AsText(g_map) as wkt
            FROM d_geometry
            WHERE ST_Contains(g_map, GeomFromText('" . $sPonto . "'))";

        $result = $this->conexao->executeSQL($sql);

        return $result;
    }

    private function findByExtensao($extensao) {

        $ponto1 = $extensao->pontoMin->x . ' ' . $extensao->pontoMin->y;
        $ponto2 = $extensao->pontoMax->x . ' ' . $extensao->pontoMin->y;
        $ponto3 = $extensao->pontoMax->x . ' ' . $extensao->pontoMax->y;
        $ponto4 = $extensao->pontoMin->x . ' ' . $extensao->pontoMax->y;

        $poligono = 'MULTIPOLYGON(((' . $ponto1 . ',' . $ponto4 . ',' . $ponto3 . ',' . $ponto2 . ',' . $ponto1 . ')))';

        $sql = "SELECT sky_geometry as id, ST_AsText(g_map) as wkt
            FROM d_geometry
            WHERE ST_Intersects(ST_GeomFromText('" . $poligono . "'), g_map)
            AND g_city IS NOT NULL";

        $result = $this->conexao->executeSQL($sql);

        return $result;
    }

    public function getExtensao($id) {

        $sql = "SELECT MAX(ST_XMAX(Box2D(ST_GeomFromText(g_map)))) as xmax,
                  MAX(ST_YMAX(Box2D(ST_GeomFromText(g_map)))) as ymax,
                  MIN(ST_XMIN(Box2D(ST_GeomFromText(g_map)))) as xmin,
                  MIN(ST_YMIN(Box2D(ST_GeomFromText(g_map)))) as ymin
                  FROM d_geometry";

        if ($id != null) {
            $sql.=" WHERE sky_geometry=" . $id;
        }

        $result = $this->conexao->executeSQL($sql);

        while ($row = pg_fetch_row($result)) {

            $max = new Ponto();
            $max->x = round($row[0], 4);
            $max->y = round($row[1], 4);

            $min = new Ponto();
            $min->x = round($row[2], 4);
            $min->y = round($row[3], 4);

            $extensao = new Extensao();
            $extensao->pontoMax = $max;
            $extensao->pontoMin = $min;

            return $extensao;
        }
        return null;
    }

    public function converterWKTtoPoligonos($wkt, $extensao) {

        //correção para zoom proporcional do município
        $distanciaX = abs($extensao->pontoMax->x - $extensao->pontoMin->x);
        $distanciaY = abs($extensao->pontoMax->y - $extensao->pontoMin->y);

        if ($distanciaX > $distanciaY) {
            $largura = $this->canvasW;
            $altura = ($distanciaY * $largura) / $distanciaX;
        } else {
            $altura = $this->canvasH;
            $largura = ($distanciaX * $altura) / $distanciaY;
        }

        $wkt = substr_replace($wkt, "", 0, 15);
        $wkt = substr($wkt, 0, -3);
        $wkt = explode(")),((", $wkt);

        //para cada poligono       
        for ($i = 0; $i < sizeof($wkt); $i++) {

            $wkt = explode(",", $wkt[$i]); //separa os pontos do poligono                
            //para cada ponto do poligono

            for ($j = 0; $j < sizeof($wkt); $j++) {

                $xy = explode(" ", $wkt[$j]); //separa o X e Y do ponto

                $xc = (round($xy[0], 4) - $extensao->pontoMin->x) / ($extensao->pontoMax->x - $extensao->pontoMin->x) * $largura;
                $yc = ((round($xy[1], 4) - $extensao->pontoMin->y) / ($extensao->pontoMax->y - $extensao->pontoMin->y) * -$altura) + $altura;

                $ponto = new Ponto();
                $ponto->x = round($xc, 4);
                $ponto->y = round($yc, 4);
                $pontos[] = $ponto;
            }
            $poligono = new Poligono();
            $poligono->pontos = $pontos;
            $poligonos[] = $poligono;
        }
        return $poligonos;
    }

    private function convertExtensao($canvasExtensao, $zoomExtensao) {

        if ($zoomExtensao != null) {
            $extensao = $zoomExtensao;
        } else {
            $extensao = $this->getExtensao(null);
        }

        $maxPonto = $this->convertCanvasPonto($canvasExtensao->pontoMax, $extensao);
        $minPonto = $this->convertCanvasPonto($canvasExtensao->pontoMin, $extensao);

        $extensao = new Extensao();
        $extensao->pontoMax = $maxPonto;
        $extensao->pontoMin = $minPonto;

        return $extensao;
    }

    private function convertCanvasPonto($ponto, $extensao) {

        $dataPonto = new Ponto();
        $dataPonto->x = ($ponto->x / $this->canvasW) * ($extensao->pontoMax->x - $extensao->pontoMin->x) + $extensao->pontoMin->x;
        $dataPonto->y = (($ponto->y - $this->canvasH) / -$this->canvasH) * ($extensao->pontoMax->y - $extensao->pontoMin->y) + $extensao->pontoMin->y;
        return $dataPonto;
    }

}

?>