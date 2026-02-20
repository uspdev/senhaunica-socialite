<div class="modal fade" id="senhaunica-socialite-editar-local-user-modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">Alterar Usuário Local</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <form class="" method="POST" id="user_local_edit" action="">
          @csrf
          @method('PUT')
          <div class="form-group row mb-2">
            <label for="name" class="col-sm-2 col-form-label">Nome</label>
            <input type="text" class="form-control w-75" id="user_name" name="name" value="">
          </div>
          <div class="form-group row mb-2">
            <label for="email" class="col-sm-2 col-form-label">E-mail</label>
            <input type="text" class="form-control w-75" id="user_email" name="email" value="">
          </div>

          <div class="form-group row mb-2">
            <label class="form-check-label col-sm-2 col-form-label" for="senha">Alterar Senha</label>
            <input type="checkbox" class="" id="senha" name="senha" value="1">
          </div>

          <div class="form-group row mb-2">
            <label for="password" class="col-sm-2 col-form-label">Senha</label>
            <input type="password" class="form-control w-75" name="password" readonly>
          </div>
          <div class="form-group row mb-2">
            <label for="password_confirmation" class="col-sm-2 col-form-label">Confirme a Senha</label>
            <input type="password" class="form-control w-75" name="password_confirmation" readonly>
          </div>


          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-sm btn-primary">Salvar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@section('javascripts_bottom')
  @parent
  <script>
    $(document).ready(function() {

      $('#senha').on('click', function(){
        $('input[type="password"]').val('').prop('readonly', function(i, val) {
          return !val;
        });
      });

    });
  </script>
@endsection
