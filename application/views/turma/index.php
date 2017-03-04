            <!-- DataTables JavaScript -->
            <script src="../vendor/datatables/js/jquery.dataTables.min.js"></script>
            <script src="../vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
            <script src="../vendor/datatables-responsive/dataTables.responsive.js"></script>

            <script src="../js/turma/script.js"></script>

            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Turma</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
             <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="alert alert-success insercao hide">
                        Turma inserida com sucesso.
                    </div>
                    <div class="alert alert-success alteracao hide">
                        Turma alterada com sucesso.
                    </div>
                    <button type="button" id="openModal" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Novo</button>
                    <!-- Modal -->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel">Nova Turma</h4>
                                </div>
                                <div class="modal-body modal-height-turma">
                                    <div class="alert alert-warning">
                                        Preencha todos os campos obrigatórios (*)
                                    </div>

                                    <div class="alert alert-danger duplicado hide">
                                        Já existe cadastro para esta turma.
                                    </div>
                                    <div class="alert alert-danger all hide">
                                        Houve um erro na inserção, favor contatar o administrador.
                                    </div>

                                    <form role="form" id="formNew" onsubmit="return false;">
                                        <input type="hidden" id="tur_id" name="tur_id" value="" />
                                        <div class="form-group pull-left input-medium">
                                            <label>Nome * </label>
                                            <input data-required="true" class="form-control" name="tur_nome" placeholder="Nome...">
                                        </div>
                                        <div class="form-group pull-left input-right input-small">
                                            <label>Ano * </label>
                                            <input data-required="true" class="form-control" name="tur_ano" placeholder="Ano...">
                                        </div>
                                        <div class="clear"></div>
                                        <div class="form-group pull-left input-semilarge">
                                            <label>Alunos * </label>
                                            <div id="alunos" style="display:block">
                                                <span id="auxAddAluno"></span>
                                                <button onclick="addAluno();" type="button" class="btn btn-success btn-circle">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="form-group pull-left input-large">
                                            <label>Disciplinas / Professores * </label>
                                            <div id="disciplinas" style="display:block">
                                                <span id="auxAddDisciplina"></span>
                                                <button onclick="addDisciplina();" type="button" class="btn btn-success btn-circle">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                        <div class="form-group pull-left input-large">
                                            <label>Status</label>
                                            <div class="radio">
                                                <label>
                                                    <input name="tur_status" id="statusRadioAtivo" value="Ativo" checked="checked" type="radio">Ativo
                                                </label>
                                                <label>
                                                    <input name="tur_status" id="statusRadioInativo" value="Inativo" type="radio">Inativo
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
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-turma">
                                <thead>
                                    <tr>
                                        <th>Ação</th>
                                        <th>Nome</th>
                                        <th>Ano</th>
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