var extensao = new Extensao();
var isMouseDown = false;

$(document).ready(function (e) {

    $('canvas').click(function (e){
        
        var checkBoxMarcado = $('input[type=radio]:checked').attr("value");
        var ponto;
        
        if(checkBoxMarcado == 'marcarPonto'){
            ponto = getCanvasPonto(e);
            obterDadosEspaciais(checkBoxMarcado, ponto);
        }
        
        if(checkBoxMarcado == 'zoomPonto'){
            salvarHistorico(); 
            ponto = getCanvasPonto(e);
            obterDadosEspaciais(checkBoxMarcado, ponto);
        }
    });


    $('canvas').mousedown(function (e){
        if($('input[type=radio]:checked').attr("value") == 'zoomExtent'){
            isMouseDown = true;
            extensao.max = getCanvasPonto(e);
        }
    });

    $('canvas').mousemove(function (e){
        if(isMouseDown){
            $('#zoomBox').show();
            $('#zoomBox').css('width','0px');
            $('#zoomBox').css('height','0px');

            var size = getCanvasPonto(e);

            if(extensao.max.x < size.x && extensao.max.y < size.y){
                $('#zoomBox').css('margin-top',extensao.max.y);
                $('#zoomBox').css('margin-left',extensao.max.x);     

                $('#zoomBox').css('width',(size.x-extensao.max.x));
                $('#zoomBox').css('height',(size.y-extensao.max.y));
            }
        }
    });

    $('canvas').mouseup(function (e){               
        if($('input[type=radio]:checked').attr("value") == 'zoomExtent'){  
            salvarHistorico(); 
            isMouseDown = false;
            extensao.min = getCanvasPonto(e);
            obterDadosEspaciais("zoomSelecao",extensao);
            $('#zoomBox').hide();

        }
    });

    $('#previous').click(function (e){
        desenharHistorico();
    })

});

function Extensao(){
    this.max = new Ponto();
    this.min = new Ponto();
}

function Ponto(){
    this.x = null;
    this.y = null;
}
