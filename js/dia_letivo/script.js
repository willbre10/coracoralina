
$(function() {
	$('.form-group input').focus(function(){
		$(this).css('border-color', '#ccc');
	})

	$('#openModal').click(function(){
		$('#gerarDias, .btn-primary.submit').removeClass('hide');
		$('.modal-dialog').css('width', '1170px');
		$('.alert-success, .alert-danger.duplicado, .alert-danger.all').addClass('hide');
		$('.modal-height-letivo').css('height', '580px');
		limparModal();
		$('#dil_dia_letivo').val('');
	})

	$(".dataBimestre").datepicker({
        monthNames: [ "Janeiro", "Fevereiro", "Março", "Abril",
                   "Maio", "Junho", "Julho", "Agosto", "Setembro",
                   "Outubro", "Novembro", "Dezembro" ],
		dateFormat: "dd/mm",
		onSelect: function(){
			var ano = $('input[name="ano"]').val();
			if (ano == ''){
				$(this).val('');
				alert("Selecione o ano primeiro.");
			}
		}
	});

	$('#ano').on('input', function (event) { 
    	this.value = this.value.replace(/[^0-9]/g, '');
	});

	$("#gerarDias").click(function () {
		var ano = $('#ano').val();

		if (yearIsValid(ano)){
			gerarDiasLetivos(ano);
		} else {
			alert('Este ano não é valido.');
			limpaDiasLetivos();
			$('#ano').val('');
		}
    })

    $("#ano").keypress(function () {
		limpaDiasLetivos();
    })

	$('.submit').click(function(){
		$('.alert-success, .alert-danger.duplicado, .alert-danger.all').addClass('hide');
        var form = $(this).attr('data-form');

		if(validaFormDiasLetivos()){
        	submitAjax(form);
		}
    })

	$('#dataTables-letivo').DataTable({
		"processing": true,
        "serverSide": true,
		"ajax": {
			url: '../dia_letivo/buscarTodosAnosLetivos',
			async: false
		},
		"columns": [
			{data: "acao"},
			{data: "ano"},
			{data: "ano_tipo"},
			{data: "ano_status"}
		]
	})
	$('thead tr[role="row"] th').first().css('width', '100px');
});

function visualizarAnoLetivo(elem){
	$('input').val('');
	var id = $('#'+elem.id).attr('data-id');

	$.ajax({
		data: 'ano_id='+id,
		url: '../dia_letivo/buscarAnoLetivo',
		type: 'POST',
		dataType: 'json',
		async: false,
		success: function(r){
			if (r){
				$('.modal-dialog').css('width', '1170px');
				$('div.modal-height-letivo').css('height', '1025px');
				preencheCamposVisualizar(r);
				$('#gerarDias, .btn-primary.submit').addClass('hide');
			} else {
				limparModal();
				$('#fecharModal').click();
				$(".alert-danger.all").removeClass('hide');
			}
		}
	})
}

function validaFormDiasLetivos(){
	var retorno = true;

	$('.form-group input, .form-group select').css('border-color', '#ccc');

	$('.form-group input[data-required="true"], .form-group select[data-required="true"]').each(function(){
		if ($(this).val() == ''){
			$(this).css('border-color', '#a94442');
			retorno = false;
		}
	})

	return retorno;
}

function submitAjax(form){
	var requisicao = 'insercao';

	$.ajax({
		data: $('#'+form).serialize(),
		url: '../dia_letivo/salvar',
		type: 'POST',
		dataType: 'json',
		async: false,
		success: function(r){
			if(r.status == 'duplicado')
				$(".alert-danger.duplicado").removeClass('hide');
			else if(r)
				regrasSucesso(requisicao);
			else
				$(".alert-danger.all").removeClass('hide');
		}
	})
}

function regrasSucesso(requisicao){
	$('#fecharModal').click();
	limparModal();
	$(".alert-success."+requisicao).removeClass('hide');
	var dtReload = $('#dataTables-letivo').DataTable();
	dtReload.ajax.reload();
}

function preencheCamposVisualizar(dados){
	var year = dados.ano[0].ano_ano;

	$('#ano_id').val(dados.ano[0].ano_id);
	$('select[name="ano_tipo"]').val(dados.ano[0].ano_tipo);

	$('input[name="ano"]').val(dados.ano[0].ano_ano).attr('readonly', 'readonly');
	gerarDiasLetivos(year);

	var cont = Object.keys(dados.dia_letivo).length;

	for(var i = 0; i < cont; i++){

		if(typeof dados.dia_letivo[i] != 'undefined'){
			var date = new Date(dados.dia_letivo[i].dil_dia_letivo + ' 00:00:00');
			var mes = parseInt(date.getMonth()) + 1;
			var valor = $('input[name="dias[hidden'+ parseInt(date.getMonth()) +'][]"]').val();
			var data_value = mes + '-' + date.getDate();

			$('li[data-value="'+ data_value +'"]').addClass('ui-selected');
			$('input[name="dias[hidden'+ parseInt(date.getMonth()) +'][]"]').val(valor+'~'+data_value);
		}
	}

	preencherDatasBimestres(dados);
}

function limparModal(){
	$('.modal-dialog input').css('border-color', '#ccc');
	$('.modal-dialog label').css('color', 'black');

	$('.form-group input[type!="radio"]').val('');
	$('.form-group select').val('');
	$('#ano').removeAttr('readonly');
	for(var i = 0; i < 12; i++){
		$('input[name="dias[hidden'+ i +'][]"]').val('');
		$('#mes'+i).html('');
	}
	$('#statusRadioAtivo').click();
}

function gerarDiasLetivos(ano){
	var days = new Array();
	var firstDayMonth = '';
	if (ano != '' && ano != null){

		for (var i = 0; i < 12; i++){
			days = getDaysMonth(ano, i);

			var firstDayMonth = new Date(ano, i, 1).getDay();
			setarDiasLayout(i, days, firstDayMonth + 1);
		}

		$('div.modal-height-letivo').css('height', '1025px');
	}
}

function getDaysMonth(ano, month){
	var date = new Date(ano, month, 1);
	var days = [];

	while (date.getMonth() === month) {
		days.push(new Date(date).getDate());
		date.setDate(date.getDate() + 1);
	}

	return days;
}

function setarDiasLayout(month, days, firstDayMonth){
	var html = '';
	var cont = days.length;
	var monthRight = parseInt(month)+1;
	var j = 1;

	while(j < firstDayMonth){
		html += '<li></li>';
		j++;
	}

	for (var i = 1; i <= cont; i++){
		html += '<li data-value="'+ monthRight + '-' + i+'" class="ui-state-default">'+ i +'</li>';
	}

	$('#mes'+month).html(html);
	$('#mes'+month).selectable({
		selected: function(event, ui){
			var valorAux = '';
			$('input[name="dias[hidden'+ month +'][]"]').val('');

			$('#mes'+month+' .ui-selected').each(function(){
				valorAux += '~'+$(this).attr('data-value')
			})

			$('input[name="dias[hidden'+ month +'][]"]').val(valorAux);
		}
	});
}

function limpaDiasLetivos(){
	$('.mes').html('');
	$('div.modal-height-letivo').css('height', '540px');	
}

function yearIsValid(year) {
	var retorno = true;
	var date = new Date(year, 1, 1);
	var dateToday = new Date();

	if(date.getFullYear() != year)
		retorno = false;

	if(dateToday.getFullYear() > year)
		retorno = false;

	return retorno;
}

function preencherDatasBimestres(dados){
	for (var i = 0; i < 4; i++){
		var id = dados.bimestre[i].bim_bimestre;
		var inicio = dados.bimestre[i].bim_inicio.split('-');
		var fim = dados.bimestre[i].bim_fim.split('-');

		$('#'+id+'b_inicio').val(inicio[2]+'/'+inicio[1]);
		$('#'+id+'b_fim').val(fim[2]+'/'+fim[1]);
	}
}