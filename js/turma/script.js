$(function() {
	$('.form-group input').focus(function(){
		$(this).css('border-color', '#ccc');
	})

	$('#openModal').click(function(){
		$('.modal-dialog').css('width', '900px');
		$('.alert-success, .alert-danger.duplicado, .alert-danger.all').addClass('hide');
		limparModal();
		autoCompleteAluno();
		autoCompleteDisciplina();
		autoCompleteProfessor();
		$('#tur_id').val('');
	})


	$('.submit').click(function(){
		$('.alert-success, .alert-danger.duplicado, .alert-danger.all').addClass('hide');
        var form = $(this).attr('data-form');

		if(validaFormTurma())
        	submitAjax(form);
    })

	$('#dataTables-turma').DataTable({
		"processing": true,
        "serverSide": true,
		"ajax": {
			url: '../turma/buscarTodasTurmasGrid',
			async: false
		},
		"columns": [
			{data: "acao"},
			{data: "tur_nome"},
			{data: "tur_ano"},
			{data: "tur_status"}
		]
	})
	$('thead tr[role="row"] th').first().css('width', '100px');
});


function autoCompleteAluno(){
	$(".autocomplete_aluno").autocomplete({
		source: function(request, response) {
			$.ajax( {
				url: "../aluno/buscarAlunoAutocomplete",
				dataType: "json",
				type: "post",
				data: {
					alu_nome: request.term,
					type_search: 'like'
				},
				success: function(data) {
					response(data);
				}
			});
		},
		select: function(event, ui){
			if (validaAlunoDuplicado($(this), ui.item.id)){
				$(this).next().val(ui.item.id);
			} else{
				alert('aluno ja selecionado, selecione outro');
				$(this).next().val('');
				event.target.value = '';
				event.preventDefault();
			}
		},
		minLength: 2
    });
}

function validaAlunoDuplicado(elem, id){
	var retorno = true;

	$('input[name="alu_id[]"]').each(function(){
  		if (id == $(this).val() && id != elem.next().val())
  			retorno = false;
	})

	return retorno;
}

function validaDisciplinaDuplicada(id){
	var retorno = true;

	$('input[name="dis_id[]"]').each(function(){
  		if (id == $(this).val())
  			retorno = false;
	})

	return retorno;
}

function autoCompleteDisciplina(){
	$(".autocomplete_disciplina").autocomplete({
		source: function(request, response) {
			$.ajax( {
				url: "../disciplina/buscarDisciplinaAutocomplete",
				dataType: "json",
				type: "post",
				data: {
					dis_nome: request.term,
					type_search: 'like'
				},
				success: function(data) {
					response(data);
				}
			});
		},
		select: function(event, ui){
			if (validaDisciplinaDuplicada(ui.item.id)){
				$(this).next().val(ui.item.id);
			} else{
				alert('Disciplina ja selecionada, selecione outra');
				event.target.value = '';
				event.preventDefault();
			}
		},
		minLength: 2
    });
}

function autoCompleteProfessor(){
	$(".autocomplete_professor").autocomplete({
		source: function(request, response) {
			$.ajax( {
				url: "../professor/buscarProfessorAutocomplete",
				dataType: "json",
				type: "post",
				data: {
					pro_nome: request.term,
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

function editarTurma(elem){
	$('.modal-dialog').css('width', '900px');
	var id = $('#'+elem.id).attr('data-id');

	$.ajax({
		data: 'tur_id='+id,
		url: '../turma/buscarTurma',
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

function validaFormTurma(){
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

	if($('#tur_id').val() != '')
		requisicao = 'alteracao';


	$.ajax({
		data: $('#'+form).serialize(),
		url: '../turma/salvar',
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
	var dtReload = $('#dataTables-turma').DataTable();
	dtReload.ajax.reload();
}

function preencheCamposEditar(dados){
	$('#tur_id').val(dados.tur_id);
	$('input[name="tur_nome"]').val(dados.tur_nome);
	$('input[name="tur_ano"]').val(dados.tur_ano);

	if(dados.tur_status == 'Inativo')
		$('#statusRadioInativo').click();
	else
		$('#statusRadioAtivo').click();

	$('#alunos input, #disciplinas input, .adicionado').remove();

	preencheCamposEditarAlunos(dados);
	preencheCamposEditarDisciplinas(dados);

	var htmlInputAluno = '<input class="form-control addAlunoEditar autocomplete_aluno btn-add-right"><input type="hidden" name="alu_id[]">';
	var htmlInputDisciplina = '<input class="form-control addDisciplinaEditar autocomplete_disciplina btn-add-right-turma"><input type="hidden" name="dis_id[]">'+
								'<input class="form-control addDisciplinaEditar autocomplete_professor btn-add-right-turma"><input type="hidden" name="pro_id[]">'+
								'<select class="form-control input-mini adicionado inline" name="tdp_quantidade_aula[]">'+
									'<option value=""></option>'+
									'<option value="1">1</option>'+
									'<option value="2">2</option>'+
								'</select>';

	$('#auxAddAluno').before(htmlInputAluno);
	$('#auxAddDisciplina').before(htmlInputDisciplina);
	autoCompleteAluno();
	autoCompleteDisciplina();
	autoCompleteProfessor();
}

function preencheCamposEditarAlunos(dados){

	var cont = dados.alunos.length;
	var html = '';

	for(var i = 0; i < cont; i++){
		html += '<input class="form-control btn-add-right" disabled="disabled" type="text" value="'+ dados.alunos[i].alu_nome +'"/>';
	}

	$('#auxAddAluno').before(html);
}

function preencheCamposEditarDisciplinas(dados){
	var cont = dados.disciplinas.length;
	var html = '';

	for(var i = 0; i < cont; i++){

		html += '<input class="form-control btn-add-right-turma" disabled="disabled" type="text" value="'+ dados.disciplinas[i].dis_nome +'"/>'+
				'<input class="form-control btn-add-right-turma" disabled="disabled" type="text" value="'+ dados.professores[i].pro_nome +'"/>'+
				'<input class="form-control input-mini inline" disabled="disabled" type="text" value="'+ dados.disciplinas[i].tdp_quantidade_aula +'"/>';
	}

	$('#auxAddDisciplina').before(html);
}

function limparModal(){
	var htmlInputAluno = '<input class="form-control autocomplete_aluno btn-add-right""><input type="hidden" name="alu_id[]">';
	var htmlInputDisciplina = '<input class="form-control autocomplete_disciplina btn-add-right-turma"><input type="hidden" name="dis_id[]">'+
								'<input class="form-control autocomplete_professor btn-add-right-turma"><input type="hidden" name="pro_id[]">'+
								'<select class="form-control adicionado input-mini inline" name="tdp_quantidade_aula[]">'+
									'<option value=""></option>'+
									'<option value="1">1</option>'+
									'<option value="2">2</option>'+
								'</select>';

	$('.form-group input[type!="radio"]').val('');
	$('#statusRadioAtivo').click();
	$('div.modal-height-turma').css('height', '375px');

	$('#alunos input, #disciplinas input, .adicionado').remove();

	if ($('#auxAddAluno').prevAll('input').length < 1)
		$('#auxAddAluno').before(htmlInputAluno);

	if ($('#auxAddDisciplina').prevAll('input').length < 1)
		$('#auxAddDisciplina').before(htmlInputDisciplina);
}

function addAluno(){
	var html = '<button type="button" onclick="removeAluno(this);" class="btn adicionado btn-danger btn-circle"><i class="fa fa-minus"></i></button>'+
				'<input class="form-control adicionado autocomplete_aluno btn-add-right ui-autocomplete-input" autocomplete="off">'+
				'<input type="hidden" name="alu_id[]">';


	$('#auxAddAluno').before(html);
	autoCompleteAluno();
}

function removeAluno(elem){
	var anterior = elem.previousElementSibling;

	if(anterior == null)
		anterior = document.getElementById('auxAddAluno');

	if (anterior.previousElementSibling != null)
		anterior.previousElementSibling.remove();

	elem.previousElementSibling.remove();
	elem.remove();
}

function addDisciplina(){
	var html = '<button type="button" onclick="removeDisciplina(this);" class="btn adicionado btn-danger btn-circle"><i class="fa fa-minus"></i></button>'+
				'<input class="form-control adicionado autocomplete_disciplina btn-add-right-turma ui-autocomplete-input" autocomplete="off">'+
				'<input type="hidden" name="dis_id[]">'+
				'<input class="form-control adicionado autocomplete_professor btn-add-right-turma ui-autocomplete-input" autocomplete="off">'+
				'<input type="hidden" name="pro_id[]">'+
				'<select class="form-control input-mini adicionado inline" name="tdp_quantidade_aula[]">'+
					'<option value=""></option>'+
					'<option value="1">1</option>'+
					'<option value="2">2</option>'+
				'</select>';


	$('#auxAddDisciplina').before(html);
	autoCompleteDisciplina();
	autoCompleteProfessor();
}

function removeDisciplina(elem){
	for (var i = 0; i < 5; i++){
		elem.previousElementSibling.remove();
	}

	elem.remove();

}