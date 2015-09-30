
(function (require,$) {
    //alert("Hola!");
   /* setTimeout(function(){
    $('#contenedor').html("Hola");
    },1000);*/  
	 
function getNumeroDias(f1,f2){
	var d1 = f1.split("-");
	var fecha1 = d1[1]+'/'+d1[2]+'/'+d1[0];	
	var d2 = f2.split("-");
	fecha2 = d2[1]+'/'+d2[2]+'/'+d2[0]; 
	var diff = Math.floor((Date.parse(fecha2) - Date.parse(fecha1)) / 86400000);	
	return diff;
}

restaFecha = function(d, fecha){ 
	aFecha = fecha.split("-");	
	var bFecha = aFecha[1]+'/'+aFecha[2]+'/'+aFecha[0];
	Fecha = new Date(bFecha);
	fechaFinal = new Date(Fecha.getTime() - (d *24 * 3600 * 1000));
	anno = fechaFinal.getFullYear();
	mes = fechaFinal.getMonth();
	dia = fechaFinal.getDate();
	mes += 1;
	mes = (mes < 10) ? ("0" + mes) : mes;
	dia = (dia < 10) ? ("0" + dia) : dia;
	var fFinal = anno+'-'+mes+'-'+dia;
	
	return(fFinal);
 }

    $( document ).ajaxComplete(function(){
       $( "#boton" ).click( function() {
	
		var flag = true;
		$('.required').each(function(){
			$(this).removeClass("empty");
			if($(this).val() == ''){
				$(this).addClass("empty");
				flag = false;
			}
		});
		if(flag == true){
		var parametros = {};
		parametros.nombre = encodeURIComponent($('#nombre').val());
		parametros.fechaInicio = encodeURIComponent($('#fechaInicio').val());
		parametros.fechaFin = encodeURIComponent($('#fechaFin').val());
		parametros.url = encodeURIComponent($('#url').val());
		parametros.module = 'TestsAB';
		parametros.action = 'insertarTest';
		
		var ajax = new ajaxHelper();
		ajax.addParams(parametros,'get');
   		ajax.setUrl('index.php?module=TestsAB&action=insertarTest&idSite=1&date='+$('#fechaInicio').val()+'&period=day');
    		ajax.setCallback(function (response) {
	        	$('#contenedorTestAB').html(response);
		});
		ajax.setFormat('html'); // the expected response format
    		ajax.send();
		}
	});
	
    $( '[id^=test]' ).click(function(){
		var id = $(this).attr('value');
		var fecha = $('#fecha'+id).val();	
		var usuariosNuevos = $('#nUsuariosNuevos'+id).val();
		var url = $('#url'+id).val();
		var aux = fecha.split(",");
		var dias = getNumeroDias(aux[0],aux[1]);
		var fFin = restaFecha(1,aux[0]);
		var fInicio = restaFecha(dias+1,aux[0]);
		var fechaAntes = fInicio+','+fFin;
	
		var parameters = {};
		parameters.module = 'TestsAB';
		parameters.action = 'getTestAB';
		parameters.fechaAntes = fechaAntes;
		parameters.url = url;
		
		//history.pushState({message:'New State!'}, 'New Title', document.location+'&fechaAntes'+fechaAntes);
		
		var ajax = new ajaxHelper();
		ajax.addParams(parameters,'get');
		ajax.setLoadingElement('#ajaxLoading');
   		ajax.setUrl('index.php?module=TestsAB&action=reporteTest&fechaAntes='+fechaAntes+'&date='+fecha+'&period=range');
    		ajax.setCallback(function (response) {
	        	$('#reportes2').html(response);
		});
		ajax.setFormat('html'); // the expected response format
    		ajax.send();		
	});
	
	 $( '[id^=eliminarTest]' ).click(function(){
		
		if(confirm("Desea eliminar el test?") == true){
		var id = $(this).attr('value');
		
		var parameters = {};
		parameters.module = 'API';
		parameters.action = 'eliminarTest';
		parameters.id = id;

		var ajax = new ajaxHelper();
		ajax.addParams(parameters,'get');
   		ajax.setUrl('index.php?module=TestsAB&action=eliminarTest&idSite=1');
    		ajax.setCallback(function (response) {
	        	$('#content').html(response);
		});
		ajax.setFormat('html'); // the expected response format
    		ajax.send();
		}		
	});

	 
    })
   })(require, jQuery); 

/*
(function (require) {
	var broadcast = require('broadcast');
	broadcast.propagateNewPage('module=Proba&action=recibir',true);	
})(require);
*/





