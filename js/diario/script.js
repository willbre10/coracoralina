$(function() {
	var today = new Date();

	$("#data").datepicker({
		monthNames: [ "Janeiro", "Fevereiro", "Março", "Abril",
                   "Maio", "Junho", "Julho", "Agosto", "Setembro",
                   "Outubro", "Novembro", "Dezembro" ],
		dateFormat: "dd/mm/yy",
		minDate: validaMinDate(),
		maxDate: today
	});

	$('#ano').on('input', function (event) { 
    	this.value = this.value.replace(/[^0-9]/g, '');
	});

	$('#lancarDiario').click(function(){
		$('input[name="editar"]').val(0);

		if ($('#auxLancamentos').html().length > 0){
			var form = $('#data-form');

			if ($('select[name="fal_quantidade_aulas"]').val() != '')
	       		submitAjax(form);
	       	else
	       		alert('Selecione a quantidade de aulas.');
	    }
	})

	$('#excluirDiario').click(function(){
		if ($('input[name="editar"]').val() == 1){
			excluirDiario();
		}
	})

	$('#buscarAlunos').click(function(){
		removeValoresEditar();

		$(".alert-success, .alert-danger").addClass('hide');
		if(validaFormDiario()){
			$('#lancarDiario').removeAttr('disabled');
			$('#excluirDiario').removeAttr('disabled');
			buscarAlunos();
			validaDiarioEditar();
		}
	})

	$('#data').change(function(){
		$('#auxLancamentos').html('');
		validaDiaLetivo($(this).val());
	})

	$('select[name="fal_bimestre"]').change(function(){
		$('#auxLancamentos').html('');
		validaDiaLetivo($('#data').val());
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

function enviaForm(){
	if(validaFormDiario()){
		if(yearIsValid($('#ano').val())){
			$('#data-form').attr('action', '../diario/imprimirDiario').submit();
		} else {
			alert('Este ano não é valido.');
			$('#ano').val('');
		}
	}

	return false;
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

function excluirDiario(){
	var tur_id = $('select[name="tur_id"]').val();
	var con_id = $('input[name="con_id"]').val();
	var tar_id = $('input[name="tar_id"]').val();
	var obs_id = $('input[name="obs_id"]').val();
	var dis_id = $('select[name="dis_id"]').val();
	var dia = $('input[name="dia_letivo"]').val();
	var bimestre = $('select[name="fal_bimestre"]').val();
	var atds_id = $('input[name="atds_id"]').val();

	$.ajax({
		data: 'tur_id='+tur_id+'&dis_id='+dis_id+'&dia='+dia+'&fal_bimestre='+bimestre+'&con_id='+con_id+'&tar_id='+tar_id+'&obs_id='+obs_id+'&atds_id='+atds_id,
		url: '../diario/excluirDiario',
		type: 'POST',
		dataType: 'json',
		async: false,
		success: function(r){

			if(r == 'excluido')
				$(".alert-success.excluido").removeClass('hide');
			else
				$(".alert-danger").removeClass('hide');

			$('#auxLancamentos').html('');
		}
	})
}

function submitAjax(form){
	$.ajax({
		data: form.serialize(),
		url: '../diario/salvar',
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

function preencheAlunos(dados){
	var cont = dados.alunos.length;
	var alunos = '';
	var numeros = '';
	var faltas = '';
	var mtop = '';
	var aniversario = '';
	var year = $('#data').val().substr(6, 4);
	var dia = $('#data').val().substr(0, 2);
	var mes = $('#data').val().substr(3, 2);
	var dataPrincipal = new Date(year, mes - 1, dia);
	var auxSunday = dataPrincipal.getDate() - dataPrincipal.getDay();
	var dataAluno = '';
	auxSunday = dataPrincipal.getDay() == 0 ? auxSunday - 6 : auxSunday + 1;

	var lastSunday = new Date(dataPrincipal.setDate(auxSunday));
	var nextMonday = new Date(dataPrincipal.setDate(auxSunday + 6));

	for(var i = 0; i < cont; i++){
		mtop = (i > 0) ? ' style="margin-top: 5px" ' : '';

		numeros += '<input disabled="disabled" class="form-control input-num-aluno" value="'+ dados.alunos[i].atd_numero_aluno +'" '+ mtop +'>';

		alunos += '<input disabled="disabled" class="form-control" value="'+ dados.alunos[i].alu_nome +'" '+ mtop +'>';

		faltas += '<input class="form-control qtd_faltas" maxlength="1" name="faltas['+ dados.alunos[i].alu_id +']" value="0" '+ mtop +'>';

		dataAluno = new Date(dados.alunos[i].alu_data_nascimento + ' 00:00:00');
		dataAluno = new Date(dataAluno.setFullYear(year));

		var auxData = dados.alunos[i].alu_data_nascimento.split('-');
		var data = auxData[2] + '/' + auxData[1];
		var icon = '';
		var pull = '';

		var first = i == 0 ? 'first' : '';
		if(dataAluno >= lastSunday && dataAluno <= nextMonday){
			icon = '<i class="fa fa-birthday-cake fa-5 '+ first +'" aria-hidden="true"></i>';
			pull = ' pull-left ';
		}

		aniversario += '<div><input disabled="disabled" class="form-control '+ pull +' input-mini" value="'+ data +'" '+ mtop +'>';

		aniversario += icon + '</div>';
	}

	var html = '<div class="form-group input-extralarge pull-left">'+
                    '<label>Conteúdo: </label>'+
                    '<textarea class="form-control" name="conteudo" rows="3" style="resize: none"></textarea>'+
                '</div>'+
                '<div class="clear"></div>'+
                '<div class="form-group input-large pull-left">'+
                    '<label>Tarefa/Casa: </label>'+
                    '<textarea class="form-control" name="tarefa" rows="2" style="resize: none"></textarea>'+
                '</div>'+
                '<div class="clear"></div>'+
                '<div class="form-group input-large pull-left">'+
                    '<label>Observações: </label>'+
                    '<textarea class="form-control" name="observacao" rows="2" style="resize: none"></textarea>'+
                '</div>'+
                '<div class="clear"></div>'+
                '<div class="form-group input-small pull-left">'+
                	'<label>Quantidade de Aulas * </label>'+
                	'<select data-required="true" class="form-control" name="fal_quantidade_aulas">'+
                		'<option value="">Selecione</option>'+
                		'<option value="1">1</option>'+
                		'<option value="2">2</option>'+
                		'<option value="3">3</option>'+
                	'</select>'+
                '</div>'+
                '<div class="clear"></div>'+
                '<!-- alunos -->'+
                '<div class="form-group">'+
                '<i class="fa fa-birthday-cake fa-5" aria-hidden="true"></i>'+
				'<label class="input-right">Aniversariantes da Semana</label>'+
				'</div>'+
				'<div class="form-group input-num-aluno pull-left">'+
                    '<label class="num-vazio"></label>'+
                    numeros+
                '</div>'+
                '<div class="form-group input-medium pull-left">'+
                    '<label>Aluno: </label>'+
                    alunos+
                '</div>'+
                '<div class="form-group input-mini pull-left input-right">'+
                    '<label>Faltas: </label>'+
                    faltas+
                '</div>'+
                '<div class="form-group input-small pull-left input-right">'+
                	'<label>Aniversário: </label>'+
                    aniversario+
                '</div>';

    $('#auxLancamentos').html(html);
    validaFaltas();
}

function validaFaltas(){
	$('.qtd_faltas').change(function(){
		if ($(this).val() > $('select[name="fal_quantidade_aulas"]').val()){
			$(this).val(0);
			alert("O número de faltas não pode ser maior que a quantidade de aulas.");
		}
	})
}

function validaFormDiario(){
	var retorno = true;

	$('.form-group input, .form-group select').css('border-color', '#ccc');

	$('input[data-required="true"], select[data-required="true"], select[name!="fal_quantidade_aulas"]').each(function(){
		if ($(this).val() == ''){
			$(this).css('border-color', '#a94442');
			retorno = false;
		}
	})

	return retorno;
}

function validaDiaLetivo(dia_letivo){
	if (dia_letivo != ''){
		var tur_id = $('select[name="tur_id"]').val();
		var bimestre = $('select[name="fal_bimestre"]').val();

		if (tur_id){
			$.ajax({
				data: 'dia_letivo='+dia_letivo+'&tur_id='+tur_id+'&fal_bimestre='+bimestre,
				url: '../dia_letivo/buscarDiaLetivo',
				type: 'POST',
				dataType: 'json',
				async: false,
				success: function(r){
					if (r.length < 1){
						$('#data').val('');
						alert('Data selecionada não é válida. A data pode não ser um dia letivo ou não fazer parte desse bimestre.');
					}
				}
			})
		} else {
			alert('Selecione uma turma antes de selecionar uma data');
			$('#data').val('');
		}
	}
}

function validaMinDate(){
	var perfil = $('input[name="per"]').val();
	var minDate = new Date(new Date().getFullYear(), 0, 1);

	//if (perfil == 4){
	if (4==5){
		var today = new Date();
		var first = today.getDate() - today.getDay();
		first = today.getDay() == 0 ? first - 6 : first + 1;

		minDate = new Date(today.setDate(first));
	}

	return minDate;
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
	$('#data').val('');

	if(tur_id){
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
}

function validaDiarioEditar(){

	var tur_id = $('select[name="tur_id"]').val();
	var bimestre = $('select[name="fal_bimestre"]').val();
	var dis_id = $('select[name="dis_id"]').val();
	var dia = $('input[name="dia_letivo"]').val();
	var perfil = $('input[name="per"]').val();

	$.ajax({
		data: 'tur_id='+tur_id+'&dis_id='+dis_id+'&dia='+dia+'&fal_bimestre='+bimestre,
		url: '../diario/buscarDiario',
		type: 'POST',
		dataType: 'json',
		async: false,
		success: function(r){
			if (r && r.conteudo){
				preencheCamposEditar(r);
				if(perfil == 4)
					removeValoresEditar();
			}
		}
	})
}

function preencheCamposEditar(dados){
	var cont = dados.faltas.length;
	var faltas = '';

	if(dados.faltas[0].fal_quantidade_aulas)
		$('input[name="editar"]').val(1);

	$('textarea[name="conteudo"]').val(dados.conteudo.con_conteudo);
	$('input[name="con_id"]').val(dados.conteudo.con_id);

	$('textarea[name="observacao"]').val(dados.observacao.obs_observacao);
	$('input[name="obs_id"]').val(dados.observacao.obs_id);

	$('textarea[name="tarefa"]').val(dados.tarefa.tar_tarefa);
	$('input[name="tar_id"]').val(dados.tarefa.tar_id);

	$('select[name="fal_quantidade_aulas"]').val(dados.faltas[0].fal_quantidade_aulas);

	for(var i = 0; i < cont; i++){
		$('input[name="faltas['+ dados.faltas[i].alu_id +']"]').val(dados.faltas[i].fal_falta);
		faltas = $('input[name="atds_id"]').val();
		$('input[name="atds_id"]').val(dados.faltas[i].fal_id + '/' + dados.faltas[i].alu_id + '@' + faltas);
	}
}

function removeValoresEditar(){
	$('input[name="con_id"], input[name="atds_id"], input[name="obs_id"], input[name="tar_id"]').val('');
	$('#excluirDiario').attr('disabled', 'disabled');
}