$(function() {
	var today = new Date();

	$('.form-group input').focus(function(){
		$(this).css('border-color', '#ccc');
	})

	$('.cep').mask('00.000-000', {reverse: true});

	$("#data_nascimento").datepicker({
		monthNames: [ "Janeiro", "Fevereiro", "Março", "Abril",
                   "Maio", "Junho", "Julho", "Agosto", "Setembro",
                   "Outubro", "Novembro", "Dezembro" ],
		dateFormat: "dd/mm/yy",
		maxDate: today
	});

	$('#openModal').click(function(){
		$('.importar').remove();
		$('.alert-success, .alert-danger.duplicado, .alert-danger.all').addClass('hide');
		limparModal();
		$('#alu_id').val('');
	})

	$('.submit').click(function(){
		$('.importar').remove();
		$('.alert-success, .alert-danger.duplicado, .alert-danger.all').addClass('hide');
        var form = $(this).attr('data-form');

		if(validaFormAluno())
        	submitAjax(form);
    })

	$('#dataTables-aluno').DataTable({
		"processing": true,
        "serverSide": true,
		"ajax": {
			url: '../aluno/buscarTodosAlunosGrid',
			async: false
		},
		"autoWidth": false,
		"columns": [
			{data: "acao"},
			{data: "alu_nome"},
			{data: "alu_rg"},
			{data: "alu_ra"},
			{data: "alu_data_nascimento"},
			{data: "alu_status"}
		]
	})
	$('thead tr[role="row"] th').first().css('width', '100px');
});

function editarAluno(elem){
	$('.importar').remove();
	limparModal();
	var id = $('#'+elem.id).attr('data-id');

	$.ajax({
		data: 'alu_id='+id,
		url: '../aluno/buscarAluno',
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

function validaFormAluno(){
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

	if($('#alu_id').val() != '')
		requisicao = 'alteracao';

	$.ajax({
		data: $('#'+form).serialize(),
		url: '../aluno/salvar',
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
	var dtReload = $('#dataTables-aluno').DataTable();
	dtReload.ajax.reload();
}

function preencheCamposEditar(dados){
	$('#alu_id').val(dados[0].alu_id);
	$('input[name="alu_nome"]').val(dados[0].alu_nome);
	$('input[name="alu_rg"]').val(dados[0].alu_rg);
	$('input[name="alu_ra"]').val(dados[0].alu_ra);

	$('input[name="alu_sexo"][value="'+ dados[0].alu_sexo +'"]').prop('checked', true);

	var auxData = dados[0].alu_data_nascimento.split('-');
	var data_nascimento = auxData[2] + '/' + auxData[1] + '/' + auxData[0];

	$("#data_nascimento").datepicker("setDate", data_nascimento);

	if(dados[0].alu_status == 'Inativo')
		$('#statusRadioInativo').click();
	else
		$('#statusRadioAtivo').click();

	if(dados[0].alu_estado)
		$('input[name="alu_estado"]').val(dados[0].alu_estado);

	if(dados[0].alu_endereco)
		$('input[name="alu_endereco"]').val(dados[0].alu_endereco);

	if(dados[0].alu_bairro)
		$('input[name="alu_bairro"]').val(dados[0].alu_bairro);

	if(dados[0].alu_numero)
		$('input[name="alu_numero"]').val(dados[0].alu_numero);

	if(dados[0].alu_cidade)
		$('input[name="alu_cidade"]').val(dados[0].alu_cidade);

	if(dados[0].alu_cep){
		var auxCep = dados[0].alu_cep.substr(0, 2) + '-' + dados[0].alu_cep.substr(2, 3) + '.' + dados[0].alu_cep.substr(5);
		$('input[name="alu_cep"]').val(auxCep);
	}

}

function limparModal(){
	$('.modal-dialog input').css('border-color', '#ccc');
	$('.modal-dialog label').css('color', 'black');
	$('.form-group input[type!="radio"]').val('');
	$('#statusRadioAtivo').click();
}