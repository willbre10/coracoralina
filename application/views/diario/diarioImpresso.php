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
                margin: 33px 0 50px 0;
                text-decoration: underline;
                width: 500px;
            }

            .diario table {
                border-collapse: collapse;
                font-family: Times;
                font-size: 15px;
                margin: 0 0 30px 0;
                width: 650px;
            }

            .conteudo h2 {
                font-family: "Comic Sans MS";
                font-size: 18px;
                font-weight: normal;
                width: 545px;
                margin: 15px 0 20px;
                float: right;
            }

            .center{
                text-align: center;
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
        <?php 
            $mes_extenso = array(
                'Jan' => 'Janeiro',
                'Feb' => 'Fevereiro',
                'Mar' => 'Março',
                'Apr' => 'Abril',
                'May' => 'Maio',
                'Jun' => 'Junho',
                'Jul' => 'Julho',
                'Aug' => 'Agosto',
                'Nov' => 'Novembro',
                'Sep' => 'Setembro',
                'Oct' => 'Outubro',
                'Dec' => 'Dezembro'
            );

            $totalAulas = 0;
        ?>
            <img src="/img/logo.png">
            <h1>Diário de Classe Colégio Cora Coralina</h1>
            <div class="diario">
                <table border="1">
                    <thead>
                        <tr>
                            <td class="center">
                                Curso: <?php echo $resultado['header1'][0]->curso; ?>
                            </td>
                            <td class="center">
                                Série: <?php echo $resultado['header1'][0]->turma; ?>
                            </td>
                            <td class="center">
                                Professor: <?php echo $resultado['header1'][0]->professor; ?>
                            </td>
                            <td class="center">
                                Disciplina: <?php echo $resultado['header1'][0]->disciplina; ?>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4">
                                <?php echo $resultado['header2'][0]->bimestre; ?>º Bimestre De <?php echo $resultado['header2'][0]->inicio->format('d'); ?> de <?php echo $mes_extenso[$resultado['header2'][0]->inicio->format('M')]; ?> a <?php echo $resultado['header2'][0]->fim->format('d'); ?> de <?php echo $mes_extenso[$resultado['header2'][0]->fim->format('M')]; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table border="1">
                    <thead>
                        <tr>
                            <td class="center">
                                DIA
                            </td>
                            <td class="center">
                                MÊS
                            </td>
                            <td class="center">
                                QTD AULAS
                            </td>
                            <td class="center">
                                RESUMO DO DIA
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resultado['corpo'] as $dado){ 
                            $auxData = explode('-', $dado->fal_dia);
                            ?>
                            <tr>
                                <td class="center">
                                    <?php echo $auxData[2]; ?>
                                </td>
                                <td class="center">
                                    <?php echo $auxData[1]; ?>
                                </td>
                                <td class="center">
                                    <?php echo $dado->fal_quantidade_aulas; ?>
                                </td>
                                <td>
                                    <?php echo $dado->con_conteudo; ?>
                                </td>
                            </tr>
                            <?php $totalAulas += $dado->fal_quantidade_aulas;
                        } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">
                                Total
                            </td>
                            <td class="center">
                                <?php echo $totalAulas; ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <input type="button" value="Imprimir" id="imprimir" onclick="imprimir();"/>
    </body>
    <script type="text/javascript">
        function imprimir(){
            window.print();
        }
    </script>
</html>