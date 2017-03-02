$(function() {
	$('.form-group input').focus(function(){
		$(this).css('border-color', '#ccc');
	})

	$('#openModal').click(function(){
		carregaPerfil();
		$('.alert-success, .alert-danger.duplicado, .alert-danger.all').addClass('hide');
		limparModal();
		$('#usu_id').val('');
	})


	$('.submit').click(function(){
		$('.alert-success, .alert-danger.duplicado, .alert-danger.all').addClass('hide');
        var form = $(this).attr('data-form');

		if(validaFormUsuario())
        	submitAjax(form);
    })

	$('#dataTables-usuario').DataTable({
		"processing": true,
        "serverSide": true,
		"ajax": {
			url: '../usuario/buscarTodosUsuariosGrid',
			async: false
		},
		"columns": [
			{data: "acao"},
			{data: "usu_login"},
			{data: "per_nome"},
			{data: "usu_status"}
		]
	})
	$('thead tr[role="row"] th').first().css('width', '100px');
});

function editarUsuario(elem){
	var id = $('#'+elem.id).attr('data-id');

	carregaPerfil();

	$.ajax({
		data: 'usu_id='+id,
		url: '../usuario/buscarUsuario',
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

function validaFormUsuario(){
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

	if($('#usu_id').val() != '')
		requisicao = 'alteracao';


	$.ajax({
		data: $('#'+form).serialize(),
		url: '../usuario/salvar',
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
	var dtReload = $('#dataTables-usuario').DataTable();
	dtReload.ajax.reload();
}

function preencheCamposEditar(dados){
	$('#usu_id').val(dados[0].usu_id);
	$('input[name="usu_login"]').val(dados[0].usu_login);
	$('select[name="per_id"]').val(dados[0].per_id);

	if(dados[0].usu_status == 'Inativo')
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

function carregaPerfil(){
	$.ajax({
		url: '../usuario/buscarPerfil',
		type: 'POST',
		dataType: 'json',
		async: false,
		success: function(r){
			var html = '<option>Selecione</option>';
			var cont = r.length;

			for(var i = 0; i < cont; i++){
				html += "<option value='"+ r[i].per_id +"'>"+ r[i].per_nome +"</option>";
			}

			$('select[name="per_id"]').html(html);
		}
	})
}