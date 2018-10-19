var canvas;
var context;
var canvasHistorico = new Array();
var extensaoHistorico = new Array();
var fileData = new Array();


function obterDadosEspaciais(func, filtro){        
       
    $.ajax({
        type: "POST",
        url: "php/src/control/control.multipoligono.php",
        data: ({
            func: func,
            filtro: filtro,
            extensao: extensaoHistorico[extensaoHistorico.length-1]
        }),
        dataType: "json",
        context: document.body,              
          
        success: function(data){
       
            canvas = document.getElementById("canvas");
            context = canvas.getContext("2d");
            context.strokeStyle  = '#000000';
            
            if(func == "obterDadosEspaciais")
                context.fillStyle = "#F5DEB3";
            if(func == "marcarPonto")
                context.fillStyle = "#F4A460";
            if(func == "zoomSelecao" || func == "zoomPonto"){
                canvas.width = canvas.width;
                context.fillStyle = "#F5DEB3";
            }
            
            for (var j in data){
                if(j == 0 && (func != "obterDadosEspaciais" || func != "marcarPonto")){
                    extensaoHistorico.push(data[j]);
                }
                desenhaMultiPoligono(data[j]);       
            }
        },
        beforeSend: function () {
            $('#loading').show();
        },
        complete: function () {
            $('#loading').hide();
        }             
    });
}

function desenhaMultiPoligono(multipoligono){
    for(var i in multipoligono.poligonos){
        desenhaPoligono(multipoligono.poligonos[i]);
    }
}

function desenhaPoligono(poligono){
    
    context.beginPath();
    context.lineWidth = 0.3;
    for (var i in poligono.pontos){
      
        if(i == 0){            
            context.moveTo(poligono.pontos[i].x,poligono.pontos[i].y);  
        }
        else{
            context.lineTo(poligono.pontos[i].x,poligono.pontos[i].y);
        }
    }
    context.fill();
    context.stroke();                        
}    

function salvarHistorico(){
    canvasHistorico.push(context.getImageData(0, 0, 500, 500));
}

function desenharHistorico(){
    
    extensaoHistorico.pop();
    
    if(canvasHistorico.length > 0){
        context.putImageData(canvasHistorico[canvasHistorico.length-1], 0, 0);
        canvasHistorico.pop();
    }
}
   
function getCanvasPonto(e){
    
    var canvas = $('#canvas').offset();

    var ponto = new Ponto();
    ponto.x = e.pageX - canvas.left;
    ponto.y = e.pageY - canvas.top;
    
    return ponto;
}

function onLoadComboBox(id){
              
    $.ajax({
        type: "POST",
        url: "php/src/control/control.raster.php",
        data: ({
            func: "list"
        }),
        dataType: "json",
        context: document.body,              
          
        success: function(data){
            for (var i in data){
                fileData.push(data[i]);
                $(id).addOption(i, i, false);
            }
        }        
    });
}

function drawBlocks(id){
    
    if(id != parseInt(id)){
        return;
    }
    
    canvas = document.getElementById("canvas");
    context = canvas.getContext("2d");   
    
    var data = fileData[id];
    var weight = data.weight;    
    var colors = weight.split(" ");  
    var size = data.blockSize;
    var numRows = data.dy;
    var numCols = data.dx;
    
    canvas.width = data.dx * size;
    canvas.height = data.dy * size;
    
    var xpos = 0; 
    var ypos = (data.dy * size) - size;        
    var color = 0;

    for(var i=0; i<=numRows; i++){
        for(var j=1; j<=numCols; j++){
            setColor(colors[color++]);
            context.fillRect(xpos,ypos,size,size);
            context.strokeRect(xpos,ypos,size,size);
            xpos+=size;
        }
        ypos-=size;
        xpos = 0;
    }
}

function setColor(weight){

    context.lineWidth = 1;   
    context.strokeStyle = "#000";
    
    if(weight == 0){
        context.fillStyle = "#FFF";
    }
    if(weight == 1){
        context.fillStyle = "#CFCFCF";
    }
    if(weight == 2){
        context.fillStyle = "#828282";
    }
    if(weight == 3){
        context.fillStyle = "#000";
    }    
}

