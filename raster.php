<head>
    <title>Visualiza��o de raster no canvas do HTML5</title>

    <script type='text/javascript' src='js/lib/jquery-1.6.2.js'></script>
    <script type='text/javascript' src='js/lib/jquery.selectboxes.min.js'></script>
    <script type='text/javascript' src='js/src/canvasFunctions.js'></script>

    <link rel="stylesheet" type="text/css" media="screen" href="include/css/style.css" />

    <script type="text/javascript">

        $(document).ready(function (e){           
            
            onLoadComboBox("#comboRasters");
            
            $("#comboRasters").change(function (e){
                
                drawBlocks($('#comboRasters :selected').text());                
                
            });
        });
        

    </script>

</head>

<body> 
    <h3>Visualiza��o de raster no canvas do HTML5</h3>
    
    <ul>
        <li>Rasters:
            <select name="rasters" id="comboRasters">
                <option value="null">Selecione uma op��o</option>
            </select>
        </li>
    </ul>  

    <canvas id="canvas"></canvas>

</body>

</html>

