<button class="btn btn-sm btn-outline-primary ml-2" id="localUser">
  <i class="fas fa-plus"></i> Adicionar Usuário Local
</button>

<div class="modal fade" id="senhaunica-socialite-adicionar-local-user-modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">Adicionar Usuário Local</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <form class="" method="POST" action="{{ route(config('senhaunica.localUserRoutes') . '.store') }}">
          @csrf
          <div class="form-group row mb-2">
            <label for="name" class="col-sm-2 col-form-label">Nome</label>
            <input type="text" class="form-control w-75" name="name" value="{{ old('name') }}">
          </div>
          <div class="form-group row mb-2">
            <label for="email" class="col-sm-2 col-form-label">E-mail</label>
            <input type="text" class="form-control w-75" name="email" value="{{ old('email') }}">
          </div>
          <div class="form-group row mb-2">
            <label for="password" class="col-sm-2 col-form-label">Senha</label>
            <input type="password" class="form-control w-75" name="password">
          </div>
          <div class="form-group row mb-2">
            <label for="password_confirmation" class="col-sm-2 col-form-label">Confirme a Senha</label>
            <input type="password" class="form-control w-75" name="password_confirmation">
          </div>
          <small class="form-text text-muted">* Todos os campos são requeridos.</small>
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

      var senhaunicaUserLocalModal = $('#senhaunica-socialite-adicionar-local-user-modal')

      $('#localUser').on('click', function() {
        senhaunicaUserLocalModal.modal();
      });

    })
  </script>
@endsection
