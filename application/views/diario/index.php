            <!-- DataTables JavaScript -->
            <script src="../vendor/datatables/js/jquery.dataTables.min.js"></script>
            <script src="../vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
            <script src="../vendor/datatables-responsive/dataTables.responsive.js"></script>

            <script src="../js/diario/script.js"></script>

            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Diário</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="alert alert-success inserido hide">
                                Diário salvo com sucesso.
                            </div>
                            <div class="alert alert-success editado hide">
                                Diário editado com sucesso.
                            </div>
                            <div class="alert alert-success excluido hide">
                                Diário excluído com sucesso.
                            </div>
                            <div class="alert alert-danger all hide">
                                Houve um erro na inserção, favor contatar o administrador.
                            </div>
                            <form id="data-form" action="#" method="post">
                                <div class="alert alert-warning">
                                    Preencha todos os campos obrigatórios (*)
                                </div>
                                <input type="hidden" name="obs_id">
                                <input type="hidden" name="tar_id">
                                <input type="hidden" name="con_id">
                                <input type="hidden" name="atds_id">
                                <input type="hidden" name="editar" value="0">
                                <input type="hidden" value="<?php echo $perfil; ?>" name="per">
                                <div class="form-group pull-left input-medium">
                                    <label>Turma * </label>
                                    <select data-required="true" class="form-control" name="tur_id">
                                        <option value="">Selecione</option>
                                    </select>
                                </div>
                                <div class="form-group pull-left input-right input-medium">
                                    <label>Disciplina * </label>
                                    <select data-required="true" class="form-control" name="dis_id">
                                        <option value="">Selecione</option>
                                    </select>
                                </div>
                                <div class="form-group pull-left input-right input-small">
                                    <label>Bimestre * </label>
                                    <select data-required="true" class="form-control" name="fal_bimestre">
                                        <option value="">Selecione</option>
                                        <option value="1">1º Bimestre</option>
                                        <option value="2">2º Bimestre</option>
                                        <option value="3">3º Bimestre</option>
                                        <option value="4">4º Bimestre</option>
                                    </select>
                                </div>
                                <div class="form-group pull-left input-small input-right">
                                    <label>Data * </label>
                                    <input data-required="true" id="data" class="form-control" name="dia_letivo">
                                </div>
                                <div class="form-group input-medium pull-left">
                                    <button type="button" class="btn btn-info" id="buscarAlunos">Buscar Alunos</button>
                                </div>
                                <div class="clear"></div>
                                <!-- conteúdo -->
                                <span id="auxLancamentos"></span>                                
                                <!-- /alunos -->
                                <div class="clear"></div>
                                <div class="form-group input-small pull-left">
                                    <button type="button" class="btn btn-info" disabled="disabled" id="lancarDiario">Lançar Diário</button>
                                </div>
                                <div class="form-group input-small pull-left">
                                    <button type="button" class="btn btn-info" disabled="disabled" id="excluirDiario">Excluir Diário</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->