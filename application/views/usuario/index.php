            <!-- DataTables JavaScript -->
            <script src="../vendor/datatables/js/jquery.dataTables.min.js"></script>
            <script src="../vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
            <script src="../vendor/datatables-responsive/dataTables.responsive.js"></script>

            <script src="../js/usuario/script.js"></script>

            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Usuário</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
             <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="alert alert-success insercao hide">
                        Usuário inserido com sucesso.
                    </div>
                    <div class="alert alert-success alteracao hide">
                        Usuário alterado com sucesso.
                    </div>
                    <button type="button" id="openModal" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Novo</button>
                    <!-- Modal -->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel">Novo Usuário</h4>
                                </div>
                                <div class="modal-body modal-height-usuario">
                                    <div class="alert alert-warning">
                                        Preencha todos os campos obrigatórios (*)
                                    </div>

                                    <div class="alert alert-danger duplicado hide">
                                        Já existe cadastro para este usuário.
                                    </div>
                                    <div class="alert alert-danger all hide">
                                        Houve um erro na inserção, favor contatar o administrador.
                                    </div>

                                    <form role="form" id="formNew">
                                        <input type="hidden" id="usu_id" name="usu_id" value="" />
                                        <div class="form-group pull-left input-medium">
                                            <label>Login * </label>
                                            <input data-required="true" class="form-control" name="usu_login" placeholder="Login...">
                                        </div>
                                        <div class="form-group pull-left input-medium input-right">
                                            <label>Senha * </label>
                                            <input data-required="true" class="form-control" name="usu_senha" type="password" placeholder="Senha...">
                                        </div>
                                        <div class="form-group pull-left input-small">
                                            <label>Perfil *</label>
                                            <select class="form-control" name="per_id">
                                                <option value="">Selecione</option>
                                            </select>
                                        </div>
                                        <div class="clear"></div>
                                        <div class="form-group pull-left input-medium">
                                            <label>Status</label>
                                            <div class="radio">
                                                <label>
                                                    <input name="usu_status" id="statusRadioAtivo" value="Ativo" checked="checked" type="radio">Ativo
                                                </label>
                                                <label>
                                                    <input name="usu_status" id="statusRadioInativo" value="Inativo" type="radio">Inativo
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
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-usuario">
                                <thead>
                                    <tr>
                                        <th>Ação</th>
                                        <th>Login</th>
                                        <th>Perfil</th>
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