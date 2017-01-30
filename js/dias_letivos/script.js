$(function() {
	$('.form-group input').focus(function(){
		$(this).css('border-color', '#ccc');
	})

	$('#openModal').click(function(){
		$('.modal-dialog').css('width', '1170px');
		$('.alert-success, .alert-danger.duplicado, .alert-danger.all').addClass('hide');
		limparModal();
		$('#dil_dia_letivo').val('');
	})

	$('#ano').on('input', function (event) { 
    	this.value = this.value.replace(/[^0-9]/g, '');
	});

	$("#ano").blur(function () {
		var ano = $(this).val();

		if (yearIsValid(ano)){
			gerarDiasLetivos(ano);
		} else {
			alert('Este ano não é valido');
			$(this).val('');
		}
    })

    $("#ano").keypress(function () {
		limpaDiasLetivos();
    })

	$('.submit').click(function(){
		$('.alert-success, .alert-danger.duplicado, .alert-danger.all').addClass('hide');
        var form = $(this).attr('data-form');

		if(validaFormDiasLetivos())
        	submitAjax(form);
    })

	$('#dataTables-letivo').DataTable({
		"processing": true,
        "serverSide": true,
		"ajax": {
			url: '../dias_letivos/buscarTodosAnosLetivos',
			async: false
		},
		"columns": [
			{data: "acao"},
			{data: "dil_dia_letivo"},
			{data: "dil_status"}
		]
	})
	$('thead tr[role="row"] th').first().css('width', '100px');
});

function editarDisciplina(elem){
	var id = $('#'+elem.id).attr('data-id');

	$.ajax({
		data: 'dis_id='+id,
		url: '../disciplina/buscarDisciplina',
		type: 'POST',
		dataType: 'json',
		async: false,
		success: function(r){
			if (r){
				preencheCamposEditar(r);
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

	$('.form-group input').css('border-color', '#ccc');

	$('.form-group input[data-required="true"]').each(function(){
		if ($(this).val() == ''){
			$(this).css('border-color', '#a94442');
			retorno = false;
		}
	})

	return retorno;
}

function submitAjax(form){
	var requisicao = 'insercao';

	// if($('#dis_id').val() != '')
		// requisicao = 'alteracao';


	$.ajax({
		data: $('#'+form).serialize(),
		url: '../dias_letivos/salvar',
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
	var dtReload = $('#dataTables-disciplina').DataTable();
	dtReload.ajax.reload();
}

function preencheCamposEditar(dados){
	$('#dis_id').val(dados[0].dis_id);
	$('input[name="dis_nome"]').val(dados[0].dis_nome);

	if(dados[0].dis_status == 'Inativo')
		$('#statusRadioInativo').click();
	else
		$('#statusRadioAtivo').click();
}

function limparModal(){
	$('.form-group input[type!="radio"]').val('');
	$('#statusRadioAtivo').click();
}

function gerarDiasLetivos(ano){
	var days = new Array();
	if (ano != '' && ano != null){

		for (var i = 0; i < 12; i++){
			days = getDaysMonth(ano, i);


			setarDiasLayout(i, days);
		}

		$('div.modal-height-letivo').css('height', '820px');
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

function setarDiasLayout(month, days){
	var html = '';
	var cont = days.length;
	var monthRight = parseInt(month)+1;

	for (var i = 1; i <= cont; i++){
		html += '<li data-value="'+ i +'/'+monthRight+'" class="ui-state-default">'+ i +'</li>';
	}

	$('#mes'+month).html(html);
	$('#mes'+month).selectable({
		selected: function(event, ui){
			var valor = $('input[name="hidden'+ month +'[]"]').val();

			$('input[name="hidden'+ month +'[]"]').val(valor+'~'+ui.selected.getAttribute('data-value'));
			ui.selected.getAttribute('data-value');
		},
		start: function(event, ui) {
			var apagar = event.target.getAttribute('data-mes');
			$('input[data-mes="'+ apagar +'"]').val('');
		},
	});
}

function limpaDiasLetivos(){
	$('.mes').html('');
	$('div.modal-height-letivo').css('height', '380px');	
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