<head>
    <title>Visualização de poligonos no canvas do HTML5</title>

    <script type='text/javascript' src='js/lib/jquery-1.6.2.js'></script>
    <script type='text/javascript' src='js/lib/jquery.selectboxes.min.js'></script>
    <script type='text/javascript' src='js/src/canvasActions.js'></script>
    <script type='text/javascript' src='js/src/canvasFunctions.js'></script>

    <link rel="stylesheet" type="text/css" media="screen" href="include/css/style.css" />

    <script type="text/javascript">
        obterDadosEspaciais("obterDadosEspaciais","Norte");
        obterDadosEspaciais("obterDadosEspaciais","Centro-Oeste");
        obterDadosEspaciais("obterDadosEspaciais","Nordeste");
        obterDadosEspaciais("obterDadosEspaciais","Sudeste");
        obterDadosEspaciais("obterDadosEspaciais","Sul");        
    </script>

</head>

<body> 
    <h3>Visualização de poligonos no canvas do HTML5</h3>

    <!--
    Regi&otilde;es:
    <input type="button" value="Norte" id="Norte" onclick="onLoadIndex(this.id)" />
    <input type="button" value="Nordeste" id="Nordeste" onclick="onLoadIndex(this.id)" />
    <input type="button" value="Sudeste" id="Sudeste" onclick="onLoadIndex(this.id)" />
    <input type="button" value="Sul" id="Sul" onclick="onLoadIndex(this.id)" />
    <input type="button" value="Centro-Oeste" id="Centro-Oeste" onclick="onLoadIndex(this.id)" />
    -->


    <div id="loading" class="loading" style="display:none">
        <div class="image"></div>
    </div>
    <div style="width:500px">
    <div class="zoomBox" id="zoomBox"></div>
    <canvas id="canvas" width="500" height="477"></canvas>
    </div>
    <ul>
        <li><input type="button" name="previous" id="previous" value="Tela Anterior" /></li>
        <li>
            <input type="radio" name="func" value="marcarPonto" /> Marcar Município
        </li>
        <li>
            <input type="radio" name="func" value="zoomPonto" /> Ver Município
        </li>
        <li>
            <input type="radio" name="func" value="zoomSelecao" /> Zoom Por Seleção
        </li>
    </ul>
    
</body>

</html>

