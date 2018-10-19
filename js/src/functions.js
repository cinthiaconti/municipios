var canvas;
var context;
var canvasHistory = new Array();
var extensaoHistory = new Array();
var fileData = new Array();


function obterDadosEspaciais(regiao){
              
    $.ajax({
        type: "POST",
        url: "php/src/control/control.multipoligono.php",
        data: ({
            func: "obterDadosEspaciais",
            filtro: regiao
        }),
        dataType: "json",
        context: document.body,              
          
        success: function(data){
       
            canvas = document.getElementById("canvas");
            context = canvas.getContext("2d");
            context.strokeStyle  = '#000000';
            context.fillStyle = "#F5DEB3";
            
            for (var j in data){   
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

function select(ponto){
              
    saveHistory();              
              
    $.ajax({
        url: "php/src/control/control.multipoligono.php",
        type: "POST",
        data: ({
            func: "selectByPonto",
            ponto: ponto,
            extensao: extensaoHistory[extensaoHistory.length-1]
        }),
        dataType: "json",
        context: document.body,              
          
        success: function(data){
            for (var j in data){
                context.fillStyle = "#F4A460";
                drawMultiPoligono(data[j], context);       
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

function zoom(ponto){
    
    saveHistory();
              
    $.ajax({
        type: "POST",
        url: "php/src/control/control.multipoligono.php",
        data: ({
            func: "zoomByPonto",
            ponto: ponto,
            extensao: extensaoHistory[extensaoHistory.length-1]
        }),
        dataType: "json",
        context: document.body,              
          
        success: function(data){
            for (var j in data){   
                canvas.width = canvas.width;
                context.fillStyle = "#F5DEB3";
                drawMultiPoligono(data[j], context);       
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

function zoomExtent(extensao){

    saveHistory(); 
    
    $.ajax({
        type: "POST",
        url: "php/src/control/control.multipoligono.php",
        data: ({
            func: "zoomByExtent",
            zoomExtent: extensao,
            extensao: extensaoHistory[extensaoHistory.length-1]
        }),
        dataType: "json",
        context: document.body,              
          
        success: function(data){
            canvas.width = canvas.width;
            context.fillStyle = "#F5DEB3";
            for (var j in data){                
                if(j == 0){
                    extensaoHistory.push(data[j]);
                }else{
                    drawMultiPoligono(data[j], context);  
                }
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

function saveHistory(){
    canvasHistory.push(context.getImageData(0, 0, 500, 500));
}

function drawHistory(){
    
    extensaoHistory.pop();
    
    if(canvasHistory.length > 0){
        context.putImageData(canvasHistory[canvasHistory.length-1], 0, 0);
        canvasHistory.pop();
    }
}
   
function getCanvasPonto(e){
    
    var canvas = $('#canvas').offset();

    var ponto = new Ponto();
    ponto.x = e.pageX - canvas.left;
    ponto.y = e.pageY - canvas.top;
    
    return ponto;
}

function Ponto(){
    this.x = null;
    this.y = null;
}

function Extent(){
    this.max = new Ponto();
    this.min = new Ponto();
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

