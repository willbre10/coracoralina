            <!-- DataTables JavaScript -->
            <script src="../vendor/datatables/js/jquery.dataTables.min.js"></script>
            <script src="../vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
            <script src="../vendor/datatables-responsive/dataTables.responsive.js"></script>

            <script src="../js/professor/script.js"></script>

            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Professor</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
             <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="alert alert-success insercao hide">
                        Professor inserido com sucesso.
                    </div>
                    <div class="alert alert-success alteracao hide">
                        Professor alterado com sucesso.
                    </div>
                    <button type="button" id="openModal" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Novo</button>
                    <!-- Modal -->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel">Novo Professor</h4>
                                </div>
                                <div class="modal-body modal-height-professor">
                                    <div class="alert alert-warning">
                                        Preencha todos os campos obrigatórios (*)
                                    </div>

                                    <div class="alert alert-danger duplicado hide">
                                        Já existe esse cpf cadastrado.
                                    </div>
                                    <div class="alert alert-danger all hide">
                                        Houve um erro na inserção, favor contatar o administrador.
                                    </div>

                                    <form role="form" id="formNew" onsubmit="return false;">
                                        <input type="hidden" id="pro_id" name="pro_id" value="" />
                                        <div class="form-group pull-left input-large">
                                            <label>Nome * </label>
                                            <input data-required="true" class="form-control" name="pro_nome" placeholder="Nome...">
                                        </div>
                                        <div class="form-group pull-left input-small">
                                            <label>RG * </label>
                                            <input data-required="true" class="form-control" name="pro_rg" placeholder="RG...">
                                        </div>
                                        <div class="form-group pull-left input-small input-right">
                                            <label>CPF * </label>
                                            <input data-required="true" class="form-control cpf" type="text" name="pro_cpf" placeholder="CPF..." maxlength="14">
                                        </div>
                                        <div class="form-group pull-left input-small input-right">
                                            <label>Data de Nascimento * </label>
                                            <input data-required="true" id="data_nascimento" class="form-control" name="pro_data_nascimento">
                                        </div>
                                        <div class="form-group pull-left input-medium">
                                            <label>Usuário * </label>
                                            <input data-required="true" class="form-control autocomplete_usuario ui-autocomplete-input" autocomplete="off">
                                            <input name="usu_id" type="hidden">
                                        </div>
                                        <div class="clear"></div>
                                        <div class="form-group pull-left input-medium">
                                            <label>Status</label>
                                            <div class="radio">
                                                <label>
                                                    <input name="pro_status" id="statusRadioAtivo" value="Ativo" checked="checked" type="radio">Ativo
                                                </label>
                                                <label>
                                                    <input name="pro_status" id="statusRadioInativo" value="Inativo" type="radio">Inativo
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
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-professor">
                                <thead>
                                    <tr>
                                        <th>Ação</th>
                                        <th>Nome</th>
                                        <th>RG</th>
                                        <th>CPF</th>
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