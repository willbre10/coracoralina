<html>
    <head>
        <meta charset="utf-8"/>
    	<style>

            @media print {    
                #imprimir{
                    display: none !important;
                }
            }

            .conteudo{
                width: 700px;
            }

            .conteudo h1 {
                float: right;
                font-family: "Comic Sans MS";
                font-size: 21px;
                font-weight: bold;
                margin: 15px 0 0 0;
                text-decoration: underline;
                width: 500px;
            }

            .conteudo h2 {
                font-family: "Comic Sans MS";
                font-size: 18px;
                font-weight: normal;
                width: 545px;
                margin: 15px 0 20px;
                float: right;
            }

            .boletim table {
                border-collapse: collapse;
                font-family: Arial,Helvetica Neue,Helvetica,sans-serif;
                font-size: 10px;
                margin: 0 0 30px 0;
            }

            .boletim table td.text-left {
                text-align: left;
            }

            .boletim table td.comic-sans {
                font-family: "Comic Sans MS";
                font-size: 13px;
            }

            .boletim table td .spacer {
                margin-left: 20px;
            }

            .boletim table td .red {
                color: #FF0000;
            }

            .container_rotate{
                position: relative;
                height: 180px;
                min-width: 30px;
            }

            .boletim table td .rotate {
                text-align: center;
                overflow: hidden;
                height: 30px;
                line-height: 30px;
                width: 200px;
                position: absolute;
                bottom: -32px;
                transform-origin: top left;
                -webkit-transform: rotate(-90deg);
                -moz-transform: rotate(-90deg);
                -ms-transform: rotate(-90deg);
                -o-transform: rotate(-90deg);
                transform: rotate(-90deg);
            }

            p.signature {
                font-family: Arial,Helvetica Neue,Helvetica,sans-serif;
                font-size: 12px;
                margin: 0 0 10px 0;
            }

            img{
                width: 80px;
                height: 80px;
                float: left;
            }

    	</style>
    </head>
    <body>
    	<div class="conteudo">
            <img src="/img/logo.png">
    		<h1>COLÉGIO CORA CORALINA</h1>
            <h2>Boletim Escolar <?php echo $resultado['header']['tur_curso']; ?> / <?php echo $resultado['header']['tur_ano']; ?></h2>
    		<div class="boletim">
    			<table border="1">
    				<thead>
    					<tr>
    						<td colspan="19" class="text-left comic-sans">
    							Aluno (a): <?php echo mb_strtoupper($resultado['header']['alu_nome']); ?>
                                <span class="spacer">Número: <?php echo $resultado['header']['atd_numero_aluno']; ?></span>
                                <span class="spacer">Turma: <?php echo $resultado['header']['tur_nome']; ?></span>
                                <span class="spacer">R.A - <?php echo $resultado['header']['alu_ra']; ?></span>
    						</td>
    					</tr>
    					<tr>
    						<td>
    							<i>Disciplinas</i>
    						</td>
    						<td class="container_rotate" height="150">
    							<div class="rotate red">NOTAS DO 1º BIMESTRE</div>
    						</td>
    						<td class="container_rotate" height="150">
    							<div class="rotate">AULAS DADAS NO BIMESTRE</div>
    						</td>
    						<td class="container_rotate" height="150">
    							<div class="rotate">FALTAS DO BIMESTRE</div>
    						</td>
    						<td class="container_rotate" height="150">
    							<div class="rotate red">NOTAS DO 2º BIMESTRE</div>
    						</td>
    						<td class="container_rotate" height="150">
    							<div class="rotate">AULAS DADAS NO BIMESTRE</div>
    						</td>
    						<td class="container_rotate" height="150">
    							<div class="rotate">FALTAS DO BIMESTRE</div>
    						</td>
    						<td class="container_rotate" height="150">
    							<div class="rotate red">NOTAS DO 3º BIMESTRE</div>
    						</td>
    						<td class="container_rotate" height="150">
    							<div class="rotate">AULAS DADAS NO BIMESTRE</div>
    						</td>
    						<td class="container_rotate" height="150">
    							<div class="rotate">FALTAS DO BIMESTRE</div>
    						</td>
    						<td class="container_rotate" height="150">
    							<div class="rotate red">NOTAS DO 4º BIMESTRE</div>
    						</td>
    						<td class="container_rotate" height="150">
    							<div class="rotate">AULAS DADAS NO BIMESTRE</div>
    						</td>
    						<td class="container_rotate" height="150">
    							<div class="rotate">FALTAS DO BIMESTRE</div>
    						</td>
    						<td class="container_rotate" height="150">
    							<div class="rotate red">MÉDIA ANUAL</div>
    						</td>
    						<td class="container_rotate" height="150">
    							<div class="rotate">TOTAL DE FALTAS</div>
    						</td>
    						<td class="container_rotate" height="150">
    							<div class="rotate red">RECUPERAÇÃO FINAL</div>
    						</td>
    						<td class="container_rotate" height="150">
    							<div class="rotate red">MÉDIA FINAL</div>
    						</td>
    						<td class="container_rotate" height="150">
    							<div class="rotate red">SITUAÇÃO FINAL</div>
    						</td>
    					</tr>
    				</thead>
    				<tbody>
                        <?php if(!empty($resultado['disciplinas'])){?>
                            <?php foreach($resultado['disciplinas'] as $disciplina){ ?>
            					<tr>
            						<td>
            							<?php echo mb_strtoupper($disciplina['dis_nome']); ?>
            						</td>
            						<td>
                                        <span
                                            <?php
                                                if (!empty($disciplina['nota1bimestre']) && $disciplina['nota1bimestre'] < "6.00")
                                                    echo 'class="red"'
                                            ?>
                                        >
                                            <?php echo $disciplina['nota1bimestre']; ?>
                                        </span>
            						</td>
            						<td>
            							<?php echo $disciplina['aulas1bimestre']; ?>
            						</td>
                                    <td>
                                        <?php echo !empty($disciplina['faltas1bimestre']) ? $disciplina['faltas1bimestre'] : 0; ?>
                                    </td>
                                    <td>
                                        <span
                                            <?php
                                                if (!empty($disciplina['nota2bimestre']) && $disciplina['nota2bimestre'] < "6.00")
                                                    echo 'class="red"'
                                            ?>
                                        >
                                            <?php echo $disciplina['nota2bimestre']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo $disciplina['aulas2bimestre']; ?>
                                    </td>
                                    <td>
                                        <?php echo !empty($disciplina['faltas2bimestre']) ? $disciplina['faltas2bimestre'] : 0; ?>
                                    </td>
                                    <td>
                                        <span
                                            <?php
                                                if (!empty($disciplina['nota3bimestre']) && $disciplina['nota3bimestre'] < "6.00")
                                                    echo 'class="red"'
                                            ?>
                                        >
                                            <?php echo $disciplina['nota3bimestre']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo $disciplina['aulas3bimestre']; ?>
                                    </td>
                                    <td>
                                        <?php echo !empty($disciplina['faltas3bimestre']) ? $disciplina['faltas3bimestre'] : 0; ?>
                                    </td>
                                    <td>
                                        <span
                                            <?php
                                                if (!empty($disciplina['nota4bimestre']) && $disciplina['nota4bimestre'] < "6.00")
                                                    echo 'class="red"'
                                            ?>
                                        >
                                            <?php echo $disciplina['nota4bimestre']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo $disciplina['aulas4bimestre']; ?>
                                    </td>
                                    <td>
                                        <?php echo !empty($disciplina['faltas4bimestre']) ? $disciplina['faltas4bimestre'] : 0; ?>
                                    </td>
                                    <td>
                                        <?php echo $disciplina['media_anual']; ?>
                                    </td>
                                    <td>
                                        <?php echo !empty($disciplina['total_faltas']) ? $disciplina['total_faltas'] : 0; ?>
                                    </td>
                                    <td>
                                        <?php echo $disciplina['recuperacao_final']; ?>
                                    </td>
                                    <td>
                                        <?php echo $disciplina['media_final']; ?>
                                    </td>
            						<td>
            							<?php echo $disciplina['situacao']; ?>
            						</td>
            					</tr>
                            <?php } ?>
                        <?php } ?>
    				</tbody>
    			</table>
    		</div>
            <p class="signature">
                CIENTE RESPONSÁVEL: _____________________________________________________________________
            </p>
    	</div>
        <br><br>
        <div class="conteudo">
            <img src="/img/logo.png">
            <h1>COLÉGIO CORA CORALINA</h1>
            <h2>Boletim Escolar <?php echo $resultado['header']['tur_curso']; ?> / <?php echo $resultado['header']['tur_ano']; ?></h2>
            <div class="boletim">
                <table border="1">
                    <thead>
                        <tr>
                            <td colspan="19" class="text-left comic-sans">
                                Aluno (a): <?php echo mb_strtoupper($resultado['header']['alu_nome']); ?>
                                <span class="spacer">Número: <?php echo $resultado['header']['atd_numero_aluno']; ?></span>
                                <span class="spacer">Turma: <?php echo $resultado['header']['tur_nome']; ?></span>
                                <span class="spacer">R.A - <?php echo $resultado['header']['alu_ra']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <i>Disciplinas</i>
                            </td>
                            <td class="container_rotate" height="150">
                                <div class="rotate red">NOTAS DO 1º BIMESTRE</div>
                            </td>
                            <td class="container_rotate" height="150">
                                <div class="rotate">AULAS DADAS NO BIMESTRE</div>
                            </td>
                            <td class="container_rotate" height="150">
                                <div class="rotate">FALTAS DO BIMESTRE</div>
                            </td>
                            <td class="container_rotate" height="150">
                                <div class="rotate red">NOTAS DO 2º BIMESTRE</div>
                            </td>
                            <td class="container_rotate" height="150">
                                <div class="rotate">AULAS DADAS NO BIMESTRE</div>
                            </td>
                            <td class="container_rotate" height="150">
                                <div class="rotate">FALTAS DO BIMESTRE</div>
                            </td>
                            <td class="container_rotate" height="150">
                                <div class="rotate red">NOTAS DO 3º BIMESTRE</div>
                            </td>
                            <td class="container_rotate" height="150">
                                <div class="rotate">AULAS DADAS NO BIMESTRE</div>
                            </td>
                            <td class="container_rotate" height="150">
                                <div class="rotate">FALTAS DO BIMESTRE</div>
                            </td>
                            <td class="container_rotate" height="150">
                                <div class="rotate red">NOTAS DO 4º BIMESTRE</div>
                            </td>
                            <td class="container_rotate" height="150">
                                <div class="rotate">AULAS DADAS NO BIMESTRE</div>
                            </td>
                            <td class="container_rotate" height="150">
                                <div class="rotate">FALTAS DO BIMESTRE</div>
                            </td>
                            <td class="container_rotate" height="150">
                                <div class="rotate red">MÉDIA ANUAL</div>
                            </td>
                            <td class="container_rotate" height="150">
                                <div class="rotate">TOTAL DE FALTAS</div>
                            </td>
                            <td class="container_rotate" height="150">
                                <div class="rotate red">RECUPERAÇÃO FINAL</div>
                            </td>
                            <td class="container_rotate" height="150">
                                <div class="rotate red">MÉDIA FINAL</div>
                            </td>
                            <td class="container_rotate" height="150">
                                <div class="rotate red">SITUAÇÃO FINAL</div>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($resultado['disciplinas'])){?>
                            <?php foreach($resultado['disciplinas'] as $disciplina){ ?>
                                <tr>
                                    <td>
                                        <?php echo mb_strtoupper($disciplina['dis_nome']); ?>
                                    </td>
                                    <td>
                                        <span
                                            <?php
                                                if (!empty($disciplina['nota1bimestre']) && $disciplina['nota1bimestre'] < "6.00")
                                                    echo 'class="red"'
                                            ?>
                                        >
                                            <?php echo $disciplina['nota1bimestre']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo $disciplina['aulas1bimestre']; ?>
                                    </td>
                                    <td>
                                        <?php echo !empty($disciplina['faltas1bimestre']) ? $disciplina['faltas1bimestre'] : 0; ?>
                                    </td>
                                    <td>
                                        <span
                                            <?php
                                                if (!empty($disciplina['nota2bimestre']) && $disciplina['nota2bimestre'] < "6.00")
                                                    echo 'class="red"'
                                            ?>
                                        >
                                            <?php echo $disciplina['nota2bimestre']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo $disciplina['aulas2bimestre']; ?>
                                    </td>
                                    <td>
                                        <?php echo !empty($disciplina['faltas2bimestre']) ? $disciplina['faltas2bimestre'] : 0; ?>
                                    </td>
                                    <td>
                                        <span
                                            <?php
                                                if (!empty($disciplina['nota3bimestre']) && $disciplina['nota3bimestre'] < "6.00")
                                                    echo 'class="red"'
                                            ?>
                                        >
                                            <?php echo $disciplina['nota3bimestre']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo $disciplina['aulas3bimestre']; ?>
                                    </td>
                                    <td>
                                        <?php echo !empty($disciplina['faltas3bimestre']) ? $disciplina['faltas3bimestre'] : 0; ?>
                                    </td>
                                    <td>
                                        <span
                                            <?php
                                                if (!empty($disciplina['nota4bimestre']) && $disciplina['nota4bimestre'] < "6.00")
                                                    echo 'class="red"'
                                            ?>
                                        >
                                            <?php echo $disciplina['nota4bimestre']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo $disciplina['aulas4bimestre']; ?>
                                    </td>
                                    <td>
                                        <?php echo !empty($disciplina['faltas4bimestre']) ? $disciplina['faltas4bimestre'] : 0; ?>
                                    </td>
                                    <td>
                                        <?php echo $disciplina['media_anual']; ?>
                                    </td>
                                    <td>
                                        <?php echo !empty($disciplina['total_faltas']) ? $disciplina['total_faltas'] : 0; ?>
                                    </td>
                                    <td>
                                        <?php echo $disciplina['recuperacao_final']; ?>
                                    </td>
                                    <td>
                                        <?php echo $disciplina['media_final']; ?>
                                    </td>
                                    <td>
                                        <?php echo $disciplina['situacao']; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <p class="signature">
                CIENTE RESPONSÁVEL: _____________________________________________________________________
            </p>
        </div>
        <input type="button" value="Imprimir" id="imprimir" onclick="imprimir();"/>
    </body>
    <script type="text/javascript">
        function imprimir(){
            window.print();
        }
    </script>
</html>