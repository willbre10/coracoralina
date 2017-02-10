$(function() {

	$('#buscarAlunos').click(function(){

		$(".alert-success, .alert-danger").addClass('hide');
		if(validaFormBoletim())
			buscarAlunos();
	})

	buscarTurmas();

	$('select[name="tur_id"]').on('change', function(){
		$('#auxLancamentos').html('');
	})

});

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
	var mtop = '';
	var mtopP = '';

	var html = 	'<!-- alunos -->'+
                '<div class="form-group input-xxlarge pull-left">'+
                    '<label class="input-medium">Aluno: </label>'+
                    // alunos+
                '</div>';

	for(var i = 0; i < cont; i++){
		mtop = (i > 0) ? ' style="margin-top: 5px" ' : '';
		mtopP = (i > 0) ? '' : ' style="margin-top: 7px !important" ';

		html += '<div class="input-xxlarge pull-left">'+
		            '<input disabled="disabled" class="form-control input-medium pull-left" value="'+ dados.alunos[i].alu_nome +'" '+ mtop +'>'+
		            '<a href="#" onclick="imprimirBoletim('+ dados.alunos[i].alu_id +')"> <p class="fa fa-print boletim fa-5" '+ mtopP +'></p></a>'+
		        '</div>';
	}

    $('#auxLancamentos').html(html);
}

function imprimirBoletim(alu_id){

	$('input[name="alu_id"]').val(alu_id);

	$('#data-form').attr('action', '../boletim/buscarBoletimAluno').submit();
}

function validaFormBoletim(){
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