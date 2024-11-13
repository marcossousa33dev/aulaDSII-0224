<div class="container">
    <div class="row">
        <div class="col-lg-8 col-lg-offset-2">
            <form id="formCadastro">
                <div class="panel panel-primary">

                    <div class="panel-heading">
                        <h4>Cadastro de usuários</h4>
                    </div>

                    <div class="panel-body">
                        <div class="form-group col-lg-6">
                            <label for="textNome" class="control-label">Usuário:</label>
                            <input name="usuario" id="usuario" class="form-control" placeholder="Digite seu Nome" type="text" required>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="inputPassword" class="control-label">Senha</label>
                            <input type="password" class="form-control" placeholder="Informe sua senha"
                                  name="senha" id="senha" data-minlength="6" required>
                        </div>
                    </div>
                    <div class="panel-footer clearfix">
                        <div class="btn-group pull-left">
                            <button type="reset" class="btn btn-lg btn-danger" id = "btnlimpar">Limpar</button>
                        </div>
                        <div class="btn-group pull-right">
                            <button type="submit" class="btn btn-lg btn-primary">Salvar</button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="panel panel-info">
                <div class="panel-heading">
                    <h1 class="panel-title">Usuários cadastrados</h1>
                </div>
                <div class="panel-body margem">
                    <table id ="tableusu"
                        data-toggle ="table"
                        data-height ="205"
                        data-search ="true"
                        accesskey=""
                        data-side-pagination ="client"
                        data-pagination ="true"
                        data-page-list="[5,10,15]"
                        data-pagination-first-text="Primeiro"
                        data-pagination-pre-text="Anterior"
                        data-pagination-next-text="Próximo"
                        data-pagination-last-text="Último"
                        data-url= 'Usuario/listar'>
                        <!--Endereço do Controller responsável em buscar os dados da lista -->
                        <thead>
                            <tr>
                                <th data-field = 'usuario' class = "col-md-3 text-left">Usuario</th>
                                <!--campo que retornará do Contoller deverá ser incluídio no data-field -->
                                <th data-field = 'senha' class = "col-md-3">Senha</th>
                                <!--campo que retornará do Contoller deverá ser incluídio no data-field -->
                                <th  class = "col-md-2" data-formatter="opcoes" data-field = "usuario">Ação</th>
                                  <!--colocaremos a função data-formatter que chamará a função JavaScript opcoes
                                    e não podemos esquecer de amarrar no data-field o campo que será o parâmetro
                                    de busca -->
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DE ATUALIZAÇÃO DO USUÁRIO -->
<div class = "modal fade" id = "alteracao" tabindex = "-1" role = "dialog" aria-labelledby = "myModalLabel" aria-hidden="true">
  <div class = "modal-dialog modal-lg">
    <div class = "modal-content">
      <div class = "modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden = "true">x</button>
        <h4 class="modal-title" id="myModalLabel">Alterar dados do usuário</h4>
      </div>
      <div class = "modal-body">
        <div class = "row">
          <div class="form-group col-xs-6 col-md-6">
            <label class = "control-label">Usuário:</label>
            <input name="musuario" id="musuario" class="form-control" placeholder="Usuário" type = "text" readonly>
          </div>

          <div class = "form-group col-xs-6 col-md-6">
            <label class = "control-label">Senha</label>
            <input class="form-control" placeholder ="Senha" name = "msenha" id = "msenha" required>
          </div>
        </div>
      </div>
      <div class = "modal-footer" style = "background-color: #A9A9A9;">
        <button type = "submit" class = "btn -btn-lg btn-primar" onclick = "altera();">Alterar</button>
        <button type = "button" class = "btn -btn-lg btn-info" data-dismiss = "modal">Sair</button>
      </div>
    </div>
  </div>
</div>



<script type="text/javascript">
    $(document).ready(function () {

        const base_url = function(url='') {
            return "<?= base_url()?>"+url
        }

        $("#formCadastro").submit(function(event) {
            $.ajax({
                type: "POST",
                url: base_url('Usuario/cadastrar'),
                data: $("#formCadastro").serialize(),
                success: function (data){
                    if ($.trim(data) == "1"){
                        $('#formCadastro').trigger("reset");
                        $('#tableusu').bootstrapTable('refresh');
                        swal({title:"OK!", text: "Dados salvo com sucesso.", type: "success"});
                    }else{
                        swal({
                            title: "Atenção !",
                            text: "Erro ao inserir, verifique!",
                            type: "error",
                        });
                    }
                },
                beforeSend: function() {
                    swal({
                        title: "Aguarde!",
                        text: "Carregando...",
                        imageUrl: "assets/img/gifs/preloader.gif",
                        showConfirmButton: false
                    });
                },
                error: function() {
                    alert("Deu Erro.")
                }
            });
            return false;
        });

        //Refresh da table com os dados a cada 5 segundos
        setInterval(function() {
            var $table = $('#tableusu');
            $table.bootstrapTable('refresh')
        },20000);
    });

    function opcoes(value) {
      var opcoes = '<button class="btn btn-xs btn-primary text-center" type = "button" onclick="busca_usuario(' + "'" + value + "'" + ');"><span class = "glyphicon glyphicon-pencil"></span></button>\n\
                    <button class="btn btn-xs btn-danger text-center" type = "button" onclick="desativa_usuario(' + "'" + value + "'" + ');"><span class = "glyphicon glyphicon-trash"></span></button>';
      return opcoes;
    }

    function busca_usuario(usuario){
      //Abrir o modal
      $('#alteracao').modal('show');

      const base_url = function(url='') {
          return "<?= base_url()?>"+url
      }

      $.ajax({
          type: "POST",
          url: base_url('Usuario/consalterar'),
          dataType: 'json',
          data: {'usuario':usuario},
          success: function (data){
            $('#musuario').val(data[0].usuario);
            $('#msenha').val(data[0].senha);
            swal.close();
          },
          beforeSend: function() {
              swal({
                  title: "Aguarde!",
                  text: "Carregando...",
                  imageUrl: "assets/img/gifs/preloader.gif",
                  showConfirmButton: false
              });
          },
          error: function() {
              alert("Deu Erro.")
          }
        });
    }

    function altera(){
      const base_url = function(url='') {
          return "<?= base_url()?>"+url
      }

      $.ajax({
          type: "POST",
          url: base_url('Usuario/alterar'),
          dataType: 'json',
          data: {'senha':$('#msenha').val(),
                 'usuario':$('#musuario').val()},
          success: function (data){
            if (data == 1) {
              swal({
                title: "OK",
                text: "Senha ALTERADA!",
                type: "success",
                showCancelButton: false,
                confirmButtonColor: "#54DD74",
                confirmButtonText: "OK!",
                closeOnConfirm: true,
                closeOnCancel: false
              },
              function(isConfirm){
                if (isConfirm) {
                  $('#tableusu').bootstrapTable('refresh');
                }
              });
              $('#alteracao').modal('hide');
            }else{
              swal({
                title: "OK",
                text: "Erro na ALTERAÇÃO, verifique!",
                type: "error",
                showCancelButton: false,
                confirmButtonColor: "#54DD74",
                confirmButtonText: "OK!",
                closeOnConfirm: false,
                closeOnCancel: false
              });
            }
          },
          beforeSend: function() {
              swal({
                  title: "Aguarde!",
                  text: "Carregando...",
                  imageUrl: "assets/img/gifs/preloader.gif",
                  showConfirmButton: false
              });
          },
          error: function() {
              alert("Deu Erro.")
          }
        });
    }

    function desativa_usuario(usuario){

      const base_url = function(url='') {
          return "<?= base_url()?>"+url
      }

      swal({
          title: "Atenção!",
          text: "Gostaria de DESATIVAR esse usuário?",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "Sim",
          cancelButtonText: "Não",
          closeOnConfirm: false,
          closeOnCancel: true},
          function (isConfirm){
            if(isConfirm){
                $.ajax({
                    url: base_url('Usuario/desativar'),
                    type: "POST",
                    data: {'usuario':usuario},
                    success: function (data){
                      if (data == 1) {
                        swal({
                          title: "OK",
                          text: "Usuário DESATIVADO!",
                          type: "success",
                          showCancelButton: false,
                          confirmButtonColor: "#54DD74",
                          confirmButtonText: "OK!",
                          closeOnConfirm: true,
                          closeOnCancel: false
                        },
                        function(isConfirm){
                          if (isConfirm) {
                            $('#tableusu').bootstrapTable('refresh');
                          }
                        });

                      }else{
                        swal({
                          title: "OK",
                          text: "Erro na DESATIVAÇÃO, verifique!",
                          type: "error",
                          showCancelButton: false,
                          confirmButtonColor: "#54DD74",
                          confirmButtonText: "OK!",
                          closeOnConfirm: false,
                          closeOnCancel: false
                        });
                      }
                    },
                    beforeSend: function() {
                        swal({
                            title: "Aguarde!",
                            text: "Carregando...",
                            imageUrl: "assets/img/gifs/preloader.gif",
                            showConfirmButton: false
                        });
                    },
                    error: function() {
                        alert("Deu Erro.")
                    }
                  });
                }
              });
    }
</script>
