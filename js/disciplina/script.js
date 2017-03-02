$(function() {
	$('.form-group input').focus(function(){
		$(this).css('border-color', '#ccc');
	})

	$('#openModal').click(function(){
		$('.alert-success, .alert-danger.duplicado, .alert-danger.all').addClass('hide');
		limparModal();
		$('#dis_id').val('');
	})


	$('.submit').click(function(){
		$('.alert-success, .alert-danger.duplicado, .alert-danger.all').addClass('hide');
        var form = $(this).attr('data-form');

		if(validaFormDisciplina())
        	submitAjax(form);
    })

	$('#dataTables-disciplina').DataTable({
		"processing": true,
        "serverSide": true,
		"ajax": {
			url: '../disciplina/buscarTodasDisciplinasGrid',
			async: false
		},
		"columns": [
			{data: "acao"},
			{data: "dis_nome"},
			{data: "dis_status"}
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
		},
		error: function(){
			window.location.href = '/home';
		}
	})
}

function validaFormDisciplina(){
	var retorno = true;

	$('.form-group input').css('border-color', '#ccc');

	$('.form-group input[data-required="true"]').each(function(){
		if ($(this).val() == ''){
			$(this).css('border-color', '#a94442').prev('label').css('color', '#a94442');
			retorno = false;
		}
	})

	return retorno;
}

function submitAjax(form){
	var requisicao = 'insercao';

	if($('#dis_id').val() != '')
		requisicao = 'alteracao';


	$.ajax({
		data: $('#'+form).serialize(),
		url: '../disciplina/salvar',
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
	$('.modal-dialog input').css('border-color', '#ccc');
	$('.modal-dialog label').css('color', 'black');
	$('.form-group input[type!="radio"]').val('');
	$('#statusRadioAtivo').click();
}