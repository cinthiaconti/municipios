<?php

include_once("../model/dao.multipoligono.php");

$func = $_REQUEST['func'];
$filtro = $_REQUEST['filtro'];
$extensao = $_REQUEST['extensao'];

if ($func == 'obterDadosEspaciais') {
    $multipoligonos = MultiPoligonoDAO::getInstancia()->obterDadosEspaciais($filtro);
    echo json_encode($multipoligonos);
}

if ($func == 'marcarPonto') {

    $ponto = new Ponto();
    $ponto->x = $filtro['x'];
    $ponto->y = $filtro['y'];

    $canvasExtensao = $extensao;

    if($canvasExtensao){
        $extensao = formatExtensao($canvasExtensao);
    }else{
        $extensao = null;
    }
    echo json_encode(MultiPoligonoDAO::getInstancia()->marcarPonto($ponto, $extensao));
}

if ($func == 'zoomPonto') {

    $ponto = new Ponto();
    $ponto->x = $filtro['x'];
    $ponto->y = $filtro['y'];
  
    $canvasExtensao = $extensao;
    
    if($extensao){            
        $extensao = formatExtensao($canvasExtensao);
    }else{
        $extensao = null;
    }

    echo json_encode(MultiPoligonoDAO::getInstancia()->zoomPonto($ponto, $extensao));
}


if ($func == 'zoomSelecao') {

    $zoomExtensao = $filtro;
    $canvasExtensao = $extensao;

    $max = new Ponto();
    $max->x = $zoomExtensao[max][x];
    $max->y = $zoomExtensao[max][y];

    $min = new Ponto();
    $min->x = $zoomExtensao[min][x];
    $min->y = $zoomExtensao[min][y];

    //conversão pra pegar os pontos em qualquer diagonal que se faça a seleção
    if ($max->x < $min->x) {
        $max->x = $zoomExtensao[min][x];
        $min->x = $zoomExtensao[max][x];
    }
    if ($max->y < $min->y) {
        $max->y = $zoomExtensao[min][y];
        $min->y = $zoomExtensao[max][y];
    }

    $Xside = $max->x - $min->x;
    $Yside = $max->y - $min->y;

    //correção que procura maior lado do retângulo 
    if ($Xside > $Yside) {
        $max->y = $min->y + $Xside;
    } else {
        $max->x = $min->x + $Yside;
    }

    $zoomExtensao = new Extensao();
    $zoomExtensao->pontoMax = $max;
    $zoomExtensao->pontoMin = $min;
    
    $extensao = formatExtensao($canvasExtensao);

    echo json_encode(MultiPoligonoDAO::getInstancia()->zoomSelecao($zoomExtensao, $extensao));
}

function formatExtensao($canvasExtensao) {

    $extensao = new Extensao();
    
    if ($canvasExtensao != null) {
        $max = new Ponto();
        $max->x = $canvasExtensao[pontoMax]['x'];
        $max->y = $canvasExtensao[pontoMin]['y'];

        $min = new Ponto();
        $min->x = $canvasExtensao[pontoMin]['x'];
        $min->y = $canvasExtensao[pontoMax]['y'];

        $extensao->pontoMax = $max;
        $extensao->pontoMin = $min;
    } else {
        $extensao = null;
    }
    return $extensao;
}

?>
