$(function() {
	var today = new Date();

	autoCompleteUsuario();

	$('.cpf').mask('000.000.000-00', {reverse: true});

	$('.form-group input').focus(function(){
		$(this).css('border-color', '#ccc');
	})

	$("#data_nascimento").datepicker({
		dateFormat: "dd/mm/yy",
		maxDate: today
	});

	$('#openModal').click(function(){
		$('.alert-success, .alert-danger.duplicado, .alert-danger.all').addClass('hide');
		limparModal();
		$('#pro_id').val('');
	})


	$('.submit').click(function(){
		$('.alert-success, .alert-danger.duplicado, .alert-danger.all').addClass('hide');
        var form = $(this).attr('data-form');

		if(validaFormProfessor())
        	submitAjax(form);
    })

	$('#dataTables-professor').DataTable({
		"processing": true,
        "serverSide": true,
		"ajax": {
			url: '../professor/buscarTodosProfessoresGrid',
			async: false
		},
		"autoWidth": false,
		"columns": [
			{data: "acao"},
			{data: "pro_nome"},
			{data: "pro_rg"},
			{data: "pro_cpf"},
			{data: "pro_data_nascimento"},
			{data: "pro_status"}
		]
	})
	$('thead tr[role="row"] th').first().css('width', '100px');
});

function editarProfessor(elem){
	var id = $('#'+elem.id).attr('data-id');

	$.ajax({
		data: 'pro_id='+id,
		url: '../professor/buscarProfessor',
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

function validaFormProfessor(){
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

	if($('#pro_id').val() != '')
		requisicao = 'alteracao';


	$.ajax({
		data: $('#'+form).serialize(),
		url: '../professor/salvar',
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
	var dtReload = $('#dataTables-professor').DataTable();
	dtReload.ajax.reload();
}

function preencheCamposEditar(dados){

	$('#pro_id').val(dados[0].pro_id);
	$('input[name="pro_nome"]').val(dados[0].pro_nome);
	$('input[name="pro_rg"]').val(dados[0].pro_rg);

	if(dados[0].usu_login){
		$('input[name="usu_id"]').val(dados[0].usu_id);
		$('.autocomplete_usuario').val(dados[0].usu_login);
	}

	// trata CPF
	var cpfReverse = dados[0].pro_cpf.split('').reverse().join('');
	var cpfAuxFinal = cpfReverse.substr(8);
	var auxCpf = cpfReverse.substr(0,2)+'-'+cpfReverse.substr(2,3)+'.'+cpfReverse.substr(5,3)+'.'+cpfAuxFinal;
	var cpf = auxCpf.split('').reverse().join('');

	$('input[name="pro_cpf"]').val(cpf);

	var auxData = dados[0].pro_data_nascimento.split('-');
	var data_nascimento = auxData[2] + '/' + auxData[1] + '/' + auxData[0];

	$("#data_nascimento").datepicker("setDate", data_nascimento);

	if(dados[0].pro_status == 'Inativo')
		$('#statusRadioInativo').click();
	else
		$('#statusRadioAtivo').click();
}

function limparModal(){
	$('.form-group input[type!="radio"]').val('');
	$('#statusRadioAtivo').click();
}

function autoCompleteUsuario(){
	$(".autocomplete_usuario").autocomplete({
		source: function(request, response) {
			$.ajax( {
				url: "../usuario/buscarUsuarioProfessor",
				dataType: "json",
				type: "post",
				data: {
					usu_nome: request.term,
					type_search: 'like'
				},
				success: function(data) {
					response(data);
				}
			});
		},
		select: function(event, ui){
			$(this).next().val(ui.item.id);
		},
		minLength: 2
    });
}