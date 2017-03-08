            
            <style>
                #feedback { font-size: 1.4em; }
                .mes .ui-selecting { background: #FECA40; }
                .mes .ui-selected { background: #F39814; color: white; }
                .mes { list-style-type: none; margin: 0; padding: 0; width: 280px; }
                .mes li { margin: 3px; padding: 1px; float: left; width: 30px; height: 25px; font-size: 16px; text-align: center; }
                #dias {width: 100%}
                .paddingMes {padding: 0px 4px 10px 0}
            </style>
            <!-- DataTables JavaScript -->
            <script src="../vendor/datatables/js/jquery.dataTables.min.js"></script>
            <script src="../vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
            <script src="../vendor/datatables-responsive/dataTables.responsive.js"></script>

            <script src="../js/dia_letivo/script.js"></script>

            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Dias Letivos</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
             <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="alert alert-success insercao hide">
                        Ano Letivo inserido com sucesso.
                    </div>
                    <div class="alert alert-success alteracao hide">
                        Ano Letivo alterado com sucesso.
                    </div>
                    <button type="button" id="openModal" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Novo</button>
                    <!-- Modal -->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel">Novo Ano Letivo</h4>
                                </div>
                                <div class="modal-body modal-height-letivo">
                                    <div class="alert alert-warning">
                                        Preencha todos os campos obrigatórios (*)
                                    </div>

                                    <div class="alert alert-danger duplicado hide">
                                        Já existe cadastro para este ano letivo.
                                    </div>
                                    <div class="alert alert-danger all hide">
                                        Houve um erro na inserção, favor contatar o administrador.
                                    </div>

                                    <form role="form" id="formNew" onsubmit="return false;">
                                        <input type="hidden" id="dil_id" name="dil_id" value="" />
                                        <div class="form-group input-small pull-left">
                                            <label>Ano * </label>
                                            <input data-required="true" maxlength="4" id="ano" class="form-control" name="ano" placeholder="Ano...">
                                        </div>
                                        <div class="form-group pull-left input-right input-small">
                                            <label>Tipo * </label>
                                            <select data-required="true" class="form-control" name="dil_tipo">
                                                <option value="">Selecione</option>
                                                <option value="1">Infantil/Fund 1</option>
                                                <option value="2">Fund 2</option>
                                            </select>
                                        </div>
                                        <div class="form-group input-small pull-left" style="margin: 25px 0 0 10px;">
                                            <button type="button" class="btn btn-info" id="gerarDias">Gerar Dias</button>
                                        </div>
                                        <div class="form-group pull-left input-medium" id="dias">
                                            <div class="pull-left paddingMes">
                                                <label>Janeiro </label>
                                                <br>
                                                <label class="dia_semana">D</label>
                                                <label class="dia_semana">S</label>
                                                <label class="dia_semana">T</label>
                                                <label class="dia_semana">Q</label>
                                                <label class="dia_semana">Q</label>
                                                <label class="dia_semana">S</label>
                                                <label class="dia_semana">S</label>
                                                <input type="hidden" name="dias[hidden0][]"/>
                                                <ol id="mes0" data-mes="0" class="mes"></ol>
                                            </div>
                                            <div class="pull-left paddingMes">
                                                <label>Fevereiro </label>
                                                <br>
                                                <label class="dia_semana">D</label>
                                                <label class="dia_semana">S</label>
                                                <label class="dia_semana">T</label>
                                                <label class="dia_semana">Q</label>
                                                <label class="dia_semana">Q</label>
                                                <label class="dia_semana">S</label>
                                                <label class="dia_semana">S</label>
                                                <input type="hidden" name="dias[hidden1][]"/>
                                                <ol id="mes1" data-mes="1" class="mes"></ol>
                                            </div>
                                            <div class="pull-left paddingMes">
                                                <label>Março </label>
                                                <br>
                                                <label class="dia_semana">D</label>
                                                <label class="dia_semana">S</label>
                                                <label class="dia_semana">T</label>
                                                <label class="dia_semana">Q</label>
                                                <label class="dia_semana">Q</label>
                                                <label class="dia_semana">S</label>
                                                <label class="dia_semana">S</label>
                                                <input type="hidden" name="dias[hidden2][]"/>
                                                <ol id="mes2" data-mes="2" class="mes"></ol>
                                            </div>
                                            <div class="pull-left paddingMes">
                                                <label>Abril </label>
                                                <br>
                                                <label class="dia_semana">D</label>
                                                <label class="dia_semana">S</label>
                                                <label class="dia_semana">T</label>
                                                <label class="dia_semana">Q</label>
                                                <label class="dia_semana">Q</label>
                                                <label class="dia_semana">S</label>
                                                <label class="dia_semana">S</label>
                                                <input type="hidden" name="dias[hidden3][]"/>
                                                <ol id="mes3" data-mes="3" class="mes"></ol>
                                            </div>
                                            <div class="clear"></div>
                                            <div class="pull-left paddingMes">
                                                <label>Maio </label>
                                                <br>
                                                <label class="dia_semana">D</label>
                                                <label class="dia_semana">S</label>
                                                <label class="dia_semana">T</label>
                                                <label class="dia_semana">Q</label>
                                                <label class="dia_semana">Q</label>
                                                <label class="dia_semana">S</label>
                                                <label class="dia_semana">S</label>
                                                <input type="hidden" name="dias[hidden4][]"/>
                                                <ol id="mes4" data-mes="4" class="mes"></ol>
                                            </div>
                                            <div class="pull-left paddingMes">
                                                <label>Junho </label>
                                                <br>
                                                <label class="dia_semana">D</label>
                                                <label class="dia_semana">S</label>
                                                <label class="dia_semana">T</label>
                                                <label class="dia_semana">Q</label>
                                                <label class="dia_semana">Q</label>
                                                <label class="dia_semana">S</label>
                                                <label class="dia_semana">S</label>
                                                <input type="hidden" name="dias[hidden5][]"/>
                                                <ol id="mes5" data-mes="5" class="mes"></ol>
                                            </div>
                                            <div class="pull-left paddingMes">
                                                <label>Julho </label>
                                                <br>
                                                <label class="dia_semana">D</label>
                                                <label class="dia_semana">S</label>
                                                <label class="dia_semana">T</label>
                                                <label class="dia_semana">Q</label>
                                                <label class="dia_semana">Q</label>
                                                <label class="dia_semana">S</label>
                                                <label class="dia_semana">S</label>
                                                <input type="hidden" name="dias[hidden6][]"/>
                                                <ol id="mes6" data-mes="6" class="mes"></ol>
                                            </div>
                                            <div class="pull-left paddingMes">
                                                <label>Agosto </label>
                                                <br>
                                                <label class="dia_semana">D</label>
                                                <label class="dia_semana">S</label>
                                                <label class="dia_semana">T</label>
                                                <label class="dia_semana">Q</label>
                                                <label class="dia_semana">Q</label>
                                                <label class="dia_semana">S</label>
                                                <label class="dia_semana">S</label>
                                                <input type="hidden" name="dias[hidden7][]"/>
                                                <ol id="mes7" data-mes="7" class="mes"></ol>
                                            </div>
                                            <div class="clear"></div>
                                            <div class="pull-left paddingMes">
                                                <label>Setembro </label>
                                                <br>
                                                <label class="dia_semana">D</label>
                                                <label class="dia_semana">S</label>
                                                <label class="dia_semana">T</label>
                                                <label class="dia_semana">Q</label>
                                                <label class="dia_semana">Q</label>
                                                <label class="dia_semana">S</label>
                                                <label class="dia_semana">S</label>
                                                <input type="hidden" name="dias[hidden8][]"/>
                                                <ol id="mes8" data-mes="8" class="mes"></ol>
                                            </div>
                                            <div class="pull-left paddingMes">
                                                <label>Outubro </label>
                                                <br>
                                                <label class="dia_semana">D</label>
                                                <label class="dia_semana">S</label>
                                                <label class="dia_semana">T</label>
                                                <label class="dia_semana">Q</label>
                                                <label class="dia_semana">Q</label>
                                                <label class="dia_semana">S</label>
                                                <label class="dia_semana">S</label>
                                                <input type="hidden" name="dias[hidden9][]"/>
                                                <ol id="mes9" data-mes="9" class="mes"></ol>
                                            </div>
                                            <div class="pull-left paddingMes">
                                                <label>Novembro </label>
                                                <br>
                                                <label class="dia_semana">D</label>
                                                <label class="dia_semana">S</label>
                                                <label class="dia_semana">T</label>
                                                <label class="dia_semana">Q</label>
                                                <label class="dia_semana">Q</label>
                                                <label class="dia_semana">S</label>
                                                <label class="dia_semana">S</label>
                                                <input type="hidden" name="dias[hidden10][]"/>
                                                <ol id="mes10" data-mes="10" class="mes"></ol>
                                            </div>
                                            <div class="pull-left paddingMes">
                                                <label>Dezembro </label>
                                                <br>
                                                <label class="dia_semana">D</label>
                                                <label class="dia_semana">S</label>
                                                <label class="dia_semana">T</label>
                                                <label class="dia_semana">Q</label>
                                                <label class="dia_semana">Q</label>
                                                <label class="dia_semana">S</label>
                                                <label class="dia_semana">S</label>
                                                <input type="hidden" name="dias[hidden11][]"/>
                                                <ol id="mes11" data-mes="11" class="mes"></ol>
                                            </div>
                                        </div>
                                        <div style="clear:both"></div>
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
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-letivo">
                                <thead>
                                    <tr>
                                        <th>Ação</th>
                                        <th>Ano</th>
                                        <th>Tipo</th>
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