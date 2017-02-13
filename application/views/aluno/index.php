            <!-- DataTables JavaScript -->
            <script src="../vendor/datatables/js/jquery.dataTables.min.js"></script>
            <script src="../vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
            <script src="../vendor/datatables-responsive/dataTables.responsive.js"></script>

            <script src="../js/aluno/script.js"></script>

            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Aluno</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
             <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <?php 
                        if (isset($status)){
                            if($status['sucesso'] > 0)
                                echo '<div class="alert alert-success importar">
                                        '. $status['sucesso'] .' aluno(s) inserido(s) com sucesso.
                                    </div>';

                            if($status['duplicado'] > 0)
                                echo '<div class="alert alert-danger importar">
                                        '. $status['duplicado'] .' aluno(s) não inserido(s) pois já existe cadastro.
                                    </div>';

                            if($status['erro'] > 0)
                                echo '<div class="alert alert-danger importar">
                                        '. $status['erro'] .' aluno(s) com erro não inserido(s).
                                    </div>';

                            if(!empty($status['arquivoErro']))
                                echo '<div class="alert alert-danger importar">
                                        '. $status['arquivoErro'] .'
                                    </div>';
                        }
                    ?>
                    <div class="alert alert-success insercao hide">
                        Aluno inserido com sucesso.
                    </div>
                    <div class="alert alert-success alteracao hide">
                        Aluno alterado com sucesso.
                    </div>
                    <button type="button" id="openModal" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Novo</button>

                    <!-- Modal -->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel">Novo Aluno</h4>
                                </div>
                                <div class="modal-body modal-height-aluno">
                                    <div class="alert alert-warning">
                                        Preencha todos os campos obrigatórios (*)
                                    </div>

                                    <div class="alert alert-danger duplicado hide">
                                        Já existe esse rg cadastrado.
                                    </div>
                                    <div class="alert alert-danger all hide">
                                        Houve um erro na inserção, favor contatar o administrador.
                                    </div>

                                    <form role="form" id="formNew" onsubmit="return false;">
                                        <input type="hidden" id="alu_id" name="alu_id" value="" />
                                        <div class="form-group pull-left input-large">
                                            <label>Nome * </label>
                                            <input data-required="true" class="form-control" name="alu_nome" placeholder="Nome...">
                                        </div>
                                        <div class="form-group pull-left input-small">
                                            <label>RG * </label>
                                            <input data-required="true" class="form-control" name="alu_rg" placeholder="RG...">
                                        </div>
                                        <div class="form-group pull-left input-small input-right">
                                            <label>Data de Nascimento * </label>
                                            <input data-required="true" id="data_nascimento" class="form-control" name="alu_data_nascimento">
                                        </div>
                                        <div class="form-group pull-left input-small input-right">
                                            <label>RA * </label>
                                            <input data-required="true" class="form-control" name="alu_ra" placeholder="RA...">
                                        </div>
                                        <div class="form-group pull-left input-medium">
                                            <label>Sexo * </label>
                                            <input name="alu_sexo" id="sexoM" value="Masculino" checked="" type="radio">Masculino
                                            <input name="alu_sexo" id="sexoF" value="Feminino" type="radio">Feminino
                                        </div>
                                        <div class="clear"></div>
                                        <div class="form-group pull-left input-small">
                                            <label>Estado </label>
                                            <select class="form-control" name="alu_estado">
                                                <option value="">Selecione</option>
                                                <option value="AC">Acre</option>
                                                <option value="AL">Alagoas</option>
                                                <option value="AP">Amapá</option>
                                                <option value="AM">Amazonas</option>
                                                <option value="BA">Bahia</option>
                                                <option value="CE">Ceará</option>
                                                <option value="DF">Distrito Federal</option>
                                                <option value="ES">Espirito Santo</option>
                                                <option value="GO">Goiás</option>
                                                <option value="MA">Maranhão</option>
                                                <option value="MS">Mato Grosso do Sul</option>
                                                <option value="MT">Mato Grosso</option>
                                                <option value="MG">Minas Gerais</option>
                                                <option value="PA">Pará</option>
                                                <option value="PB">Paraíba</option>
                                                <option value="PR">Paraná</option>
                                                <option value="PE">Pernambuco</option>
                                                <option value="PI">Piauí</option>
                                                <option value="RJ">Rio de Janeiro</option>
                                                <option value="RN">Rio Grande do Norte</option>
                                                <option value="RS">Rio Grande do Sul</option>
                                                <option value="RO">Rondônia</option>
                                                <option value="RR">Roraima</option>
                                                <option value="SC">Santa Catarina</option>
                                                <option value="SP">São Paulo</option>
                                                <option value="SE">Sergipe</option>
                                                <option value="TO">Tocantins</option>
                                            </select>
                                        </div>
                                        <div class="form-group pull-left input-large">
                                            <label>Endereço </label>
                                            <input class="form-control" name="alu_endereco" placeholder="Endereço...">
                                        </div>
                                        <div class="form-group pull-left input-medium">
                                            <label>Bairro </label>
                                            <input class="form-control" name="alu_bairro" placeholder="Bairro...">
                                        </div>
                                        <div class="form-group pull-left input-medium input-right">
                                            <label>Cidade </label>
                                            <input class="form-control" name="alu_cidade" placeholder="Cidade...">
                                        </div>
                                        <div class="form-group pull-left input-small">
                                            <label>Número </label>
                                            <input class="form-control" name="alu_numero" placeholder="Número...">
                                        </div>
                                        <div class="form-group pull-left input-small input-right">
                                            <label>CEP </label>
                                            <input class="form-control cep" name="alu_cep" placeholder="CEP...">
                                        </div>
                                        <div class="clear"></div>
                                        <div class="form-group pull-left input-medium">
                                            <label>Status</label>
                                            <div class="radio">
                                                <label>
                                                    <input name="alu_status" id="statusRadioAtivo" value="Ativo" checked="checked" type="radio">Ativo
                                                </label>
                                                <label>
                                                    <input name="alu_status" id="statusRadioInativo" value="Inativo" type="radio">Inativo
                                                </label>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="modal-footer clear">
                                    <button type="button" class="btn btn-default" data-dismiss="modal" id="fecharModal">Fechar</button>
                                    <button type="button" class="btn btn-primary submit" data-form="formNew">Salvar</button>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>
                    <!-- /.modal -->
                    <br><br>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-aluno">
                                <thead>
                                    <tr>
                                        <th>Ação</th>
                                        <th>Nome</th>
                                        <th>RG</th>
                                        <th>RA</th>
                                        <th>Data de Nascimento</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <form role="form-importar" id="formImportar" action="/aluno/importar" method="post" enctype="multipart/form-data">
                                <div class="form-group pull-left input-large">
                                    <label>Importar Aluno </label>
                                    <input class="input-medium" type="file" name="importacao">
                                </div>
                                <div class="input-large">
                                    <button type="submit" id="openImportar" class="btn btn-info">Importar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>