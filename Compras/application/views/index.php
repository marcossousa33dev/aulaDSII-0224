<!DOCTYPE html>
<html>

<head>
  <title>Login - Sistema de Compras</title>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <link rel="stylesheet" href="<?php echo base_url("assets/css/bootstrap.min.css"); ?>" type="text/css" />
  <link rel="stylesheet" href="<?php echo base_url("assets/css/sweetalert2.min.css"); ?>" type="text/css" />
  <link rel="stylesheet" href="<?php echo base_url("assets/css/style.css"); ?>"  type="text/css" />

</head>

<body class="h-100 bg-primary">
  <div class="container-fluid d-flex align-items-center justify-content-center">
    <div class="content bg-light">
      <h1 class="text-center">Acesso ao sistema</h1>
      <form method="post">
        <div class="form-group mb-4">
          <label for="loginTxt" class="control-label">Usuario:</label>
          <input class="form-control bg-primary text-light"
                 placeholder="Digite seu usuário"
                 name="loginTxt"
                 id="loginTxt"
                 maxlength="15"/>
        </div>
        <div class="form-group mb-4">
          <label for="senhaTxt" class="control-label">Senha:</label>
          <input type="password" 
                 class="form-control bg-primary text-light"
                 placeholder="Digite sua senha"
                 name="senhaTxt"
                 id="senhaTxt"
                 maxlength="20" />
        </div>
        <div class="row m-0 p-0">
          <button type="button"
                  id="loginBtn"
                  class="btn btn-success btn-block">
            ACESSAR
          </button>
        </div>
      </form>
    </div>
  </div>
</body>

<script src="<?php echo base_url("assets/js/jquery-3.6.0.min.js"); ?>" type="text/javascript"></script>
<script src="<?php echo base_url("assets/js/sweetalert2.all.min.js"); ?>" type="text/javascript"></script>
<script src="<?php echo base_url("assets/js/bootstrap.min.js"); ?>" type="text/javascript"></script>

<script type="text/javascript" charset="utf-8">
  var base_url = "<?= base_url();?>"
  $(document).ready(function() {
    $('#loginBtn').on('click', async function(e) {
      e.preventDefault();

      const config = {
        method: "post",
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          usuario: $('#loginTxt').val(),
          senha: $('#senhaTxt').val()
        })
      };

      const request = await fetch(base_url + 'login/logar', config);
      const response = await request.json();

      if (response.codigo == 1) {

        Swal.fire({
          title:'Acesso liberado',
          text: 'Bem-vindo ao sistema de Compras.',
          icon: 'success'
        });                
      }
      else {
        Swal.fire({
          title: 'Atenção!', 
          text: response.codigo + ' - ' + response.msg, 
          icon: 'error'
        });
      }
    });
  });
</script>
</body>
</html>
