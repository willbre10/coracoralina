$(function() {

	$('#lancarNota').click(function(){
		var form = $('#data-form');

		if (validaNota())
       		submitAjax(form);
	})

	$('#buscarAlunos').click(function(){
		removeValoresEditar();

		$(".alert-success, .alert-danger").addClass('hide');
		if(validaFormNota()){
			$('#lancarNota').removeAttr('disabled');
			buscarAlunos();
			validaNotaEditar();
		}
	})

	buscarTurmas();

	$('select[name="tur_id"]').on('change', function(){
		$('#auxLancamentos').html('');
		buscarDisciplinaPorTurmaProfessor($(this).val());
	})

	$('select[name="dis_id"]').on('change', function(){
		$('#auxLancamentos').html('');
	})
});

function submitAjax(form){
	$.ajax({
		data: form.serialize(),
		url: '../nota/salvar',
		type: 'POST',
		dataType: 'json',
		async: false,
		success: function(r){
			if(r == 'editado')
				$(".alert-success.editado").removeClass('hide');
			else if (r)
				$(".alert-success.inserido").removeClass('hide');
			else
				$(".alert-danger").removeClass('hide');

			$('#auxLancamentos').html('');
		}
	})
}

function buscarAlunos(){
	var turma = $('select[name="tur_id"]').val();

	$.ajax({
		data: 'tur_id='+turma,
		url: '../turma/buscarTurma',
		type: 'POST',
		dataType: 'json',
		async: false,
		success: function(r){
			if(r){
				preencheAlunos(r);
			}
		}
	})
}

function blurNota(){
	$('.nota').blur(function(){
		$('.total[data-aluno="'+ alu_id +'"]').val('');
		var alu_id = $(this).attr('data-aluno');
		var soma = 0;

		$('.nota[data-aluno="'+ alu_id +'"]').each(function(){
			soma = parseFloat(soma) + parseFloat($(this).val());
		})

		var total = (parseFloat(soma) / 2);
		if (!isNaN(total)){
			$('.total[data-aluno="'+ alu_id +'"]').val(total.toFixed(2));

			var simulado = $('.notasm[data-aluno="'+ alu_id +'"]').val();
			var media = parseFloat(simulado) + parseFloat(total);
			var $notaMedia = $('.notaMedia[data-aluno="'+ alu_id +'"]');

			if (simulado == '')
				$notaMedia.val(parseFloat(total).toFixed(2));
			else if(media > 10)
				$notaMedia.val('10.00');
			else
				$notaMedia.val(media.toFixed(2));

		}
	})
}


function blurNotaSimulado(){
	$('.notasm').blur(function(){
		var alu_id = $(this).attr('data-aluno');
		var total = $('.total[data-aluno="'+ alu_id +'"]').val();
		var media = parseFloat($(this).val()) + parseFloat(total);
		var $notaMedia = $('.notaMedia[data-aluno="'+ alu_id +'"]');

		if ($(this).val() == '')
			$notaMedia.val(parseFloat(total).toFixed(2));
		else if (media > 10)
			$notaMedia.val('10.00');
		else
			$notaMedia.val(media.toFixed(2));
	})
}

function preencheAlunos(dados){
	var cont = dados.alunos.length;
	var mtop = '';

	var html = 	'<!-- alunos -->'+
				'<div class="alert alert-warning">'+
                    'As notas das PROVAS devem ser lançadas entre 0.00 e 7.00.<br>'+
                    'As notas dos TRABALHOS devem ser lançadas entre 0.00 e 3.00.'+
                '</div>'+
                '<div class="form-group">'+
                '<div class="form-group input-xxlarge pull-left">'+
                	'<label class="input-num-aluno"></label>'+
                	// numero_aluno+
                    '<label class="input-medium">Aluno: </label>'+
                    // alunos+
                    '<label class="input-notas input-right">Prova Mensal: </label>'+
                    // provam+
                    '<label class="input-notas input-right">Avaliativa Mensal: </label>'+
                    // trabalhom+
                    '<label class="input-notas input-right">Prova Bimestral: </label>'+
                    // provab+
                    '<label class="input-notas input-right">Avaliativa Bimestral: </label>'+
                    // trabalhob+
                    '<label class="input-notas input-right">Total: </label>'+
                    // total+
                    '<label class="input-notas input-right">Simulado: </label>'+
                    // simulado+
                    '<label class="input-notas input-right">Média: </label>'+
                    // notaMedia+
                '</div>';

	for(var i = 0; i < cont; i++){
		mtop = (i > 0) ? ' style="margin-top: 5px" ' : '';

		html += '<div class="input-xxlarge pull-left">'+
					'<input disabled="disabled" class="form-control input-num-aluno pull-left" value="'+ dados.alunos[i].atd_numero_aluno +'" '+ mtop +'>'+
		            '<input disabled="disabled" class="form-control input-medium pull-left" value="'+ dados.alunos[i].alu_nome +'" '+ mtop +'>'+
		            '<input class="form-control prova nota input-notas pull-left input-right" data-aluno="'+ dados.alunos[i].alu_id +'" maxlength="5" name="notas[pm]['+ dados.alunos[i].alu_id +']" placeholder="00.00" '+ mtop +'>'+
		            '<input class="form-control trabalho nota input-notas pull-left input-right" data-aluno="'+ dados.alunos[i].alu_id +'" maxlength="5" name="notas[tm]['+ dados.alunos[i].alu_id +']" placeholder="00.00" '+ mtop +'>'+
		            '<input class="form-control prova nota input-notas pull-left input-right" data-aluno="'+ dados.alunos[i].alu_id +'" maxlength="5" name="notas[pb]['+ dados.alunos[i].alu_id +']" placeholder="00.00" '+ mtop +'>'+
		            '<input class="form-control trabalho nota input-notas pull-left input-right" data-aluno="'+ dados.alunos[i].alu_id +'" maxlength="5" name="notas[tb]['+ dados.alunos[i].alu_id +']" placeholder="00.00" '+ mtop +'>'+
		            '<input class="form-control input-notas total pull-left input-right" disabled="disabled" data-aluno="'+ dados.alunos[i].alu_id +'" placeholder="00.00" '+ mtop +'>'+
		            '<input class="form-control simulado notasm input-notas pull-left input-right" data-aluno="'+ dados.alunos[i].alu_id +'" maxlength="5" name="notas[sm]['+ dados.alunos[i].alu_id +']" placeholder="00.00" '+ mtop +'>'+
		            '<input class="form-control input-notas notaMedia pull-left input-right" disabled="disabled" data-aluno="'+ dados.alunos[i].alu_id +'" placeholder="00.00" '+ mtop +'>'+
		        '</div>';
	}

    $('#auxLancamentos').html(html);

    $('.nota').mask('00.00', {reverse: true});
    $('.notasm').mask('00.00', {reverse: true});
    blurNota();
    blurNotaSimulado();
}

function validaNota(){
	var retorno = true;
	$('.nota').css('border-color', '#ccc');

	$('.nota').each(function(){
		var valor = $(this).val().replace('.', '')

		var valida = $(this).hasClass('prova') ? verificaNotaValidaProva(valor) : verificaNotaValidaTrabalho(valor);
		if(!valida){
			retorno = false;
			$(this).css('border-color', 'red');
		}
	})

	$('.notasm').each(function(){
		var valor = $(this).val().replace('.', '')

		var valida = verificaNotaValidaSimulado(valor);
		if(!valida){
			retorno = false;
			$(this).css('border-color', 'red');
		}
	})

	return retorno;
}

function verificaNotaValidaProva(valor){
	var retorno = true;
	var cont = valor.length;
	var notaSplit = valor.split('');

	if(cont == 3){
		if(valor > 700){
			$(this).css('border-color', 'red');
			retorno = false;
		}
	} else {
		$(this).css('border-color', 'red');
		retorno = false;
	}

	return retorno;
}

function verificaNotaValidaTrabalho(valor){
	var retorno = true;
	var cont = valor.length;
	var notaSplit = valor.split('');

	if(cont == 3){
		if(valor > 300){
			$(this).css('border-color', 'red');
			retorno = false;
		}
	} else {
		$(this).css('border-color', 'red');
		retorno = false;
	}

	return retorno;
}

function validaFormNota(){
	var retorno = true;

	$('.form-group input, .form-group select').css('border-color', '#ccc');

	$('input[data-required="true"], select[data-required="true"]').each(function(){
		if ($(this).val() == ''){
			$(this).css('border-color', '#a94442');
			retorno = false;
		}
	})

	return retorno;
}

function verificaNotaValidaSimulado(valor){
	var retorno = true;
	var cont = valor.length;
	var notaSplit = valor.split('');

	if(cont == 3){
		if(valor > 200){
			$(this).css('border-color', 'red');
			retorno = false;
		}
	} else if(cont > 0) {
		$(this).css('border-color', 'red');
		retorno = false;
	}

	return retorno;
}

function buscarTurmas(){
	$.ajax({
		url: '../turma/buscarTurmaPerfil',
		type: 'POST',
		dataType: 'json',
		async: false,
		success: function(r){
			if (r){
				var html = '<option value="">Selecione</option>';
				var cont = r.length;

				for(var i = 0; i < cont; i++){
					html += "<option value='"+ r[i].tur_id +"'>"+ r[i].tur_nome +" "+ r[i].tur_ano +"</option>";
				}

				$('select[name="tur_id"]').html(html);
			}
		}
	})
}

function buscarDisciplinaPorTurmaProfessor(tur_id){
	$.ajax({
		data: 'tur_id='+tur_id,
		url: '../disciplina/buscarDisciplinaPorTurmaProfessor',
		type: 'POST',
		dataType: 'json',
		async: false,
		success: function(r){
			if (r){
				var html = '<option value="">Selecione</option>';
				var cont = r.length;

				for(var i = 0; i < cont; i++){
					html += "<option value='"+ r[i].dis_id +"'>"+ r[i].dis_nome +"</option>";
				}

				$('select[name="dis_id"]').html(html);
			}
		}
	})
}

function validaNotaEditar(){

	var tur_id = $('select[name="tur_id"]').val();
	var dis_id = $('select[name="dis_id"]').val();
	var perfil = $('input[name="per"]').val();
	var bimestre = $('select[name="bimestre"]').val();

	$.ajax({
		data: 'tur_id='+tur_id+'&dis_id='+dis_id+'&bimestre='+bimestre,
		url: '../nota/buscarNota',
		type: 'POST',
		dataType: 'json',
		async: false,
		success: function(r){
			if (r.length > 0){
				preencheCamposEditar(r);
				if(perfil == 4)
					removeValoresEditar();
			}
		}
	})
}

function preencheCamposEditar(dados){
	var cont = dados.length;
	var notas = '';

	for(var i = 0; i < cont; i++){
		$('input[name="notas[pm]['+ dados[i].alu_id +']"]').val(dados[i].not_prova_mensal);
		$('input[name="notas[tm]['+ dados[i].alu_id +']"]').val(dados[i].not_trabalho_mensal);
		$('input[name="notas[pb]['+ dados[i].alu_id +']"]').val(dados[i].not_prova_bimestral);
		$('input[name="notas[tb]['+ dados[i].alu_id +']"]').val(dados[i].not_trabalho_bimestral);
		$('input[name="notas[sm]['+ dados[i].alu_id +']"]').val(dados[i].not_simulado);

		notas = $('input[name="atds_id"]').val();
		$('input[name="atds_id"]').val(dados[i].not_id + '/' + dados[i].alu_id + '@' + notas);
	}

	$('.nota').each(function(){
		$(this).blur();
	})

	$('.notasm').each(function(){
		$(this).blur();
	})
}

function removeValoresEditar(){
	$('input[name="atds_id"]').val('');
	$('#lancarNota').attr('disabled', 'disabled');
}