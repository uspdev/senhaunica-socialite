<button data-user-id="{{ $user->id }}" data-route="{{ route('senhaunica-users.show', $user->id) }}"
  class="btn btn-sm btn-outline-primary senhaunicaUserPermissionBtn">
  {{ $user->categorias() }}
</button>

<div class="modal fade" id="senhaunica-users-permission-modal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">
          Premissões de <span class="font-weight-bold name"></span>
          Não está salvando nada ainda
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="" method="POST" action="{{ route(config('senhaunica.userRoutes') . '.store') }}"
          data-ajax="{{ route('SenhaunicaFindUsers') }}">
          @csrf
          <div class="row">
            <div class="col-md-6">
              <div class="font-weight-bold mb-2">
                Permissões da aplicação<br>
                Criar checkbox com a lista se disponível
              </div>
              <div class="permissao-app ml-2">
                Sem permissões disponíveis
              </div>
            </div>
            <div class="col-md-6">

              <div class="">
                <div class="font-weight-bold mb-2">
                  Permissões hierárquicas
                </div>
                <div class="ml-2">
                  <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-outline-success active">
                      <input type="radio" name="level" value="user" autocomplete="off" checked> Usuário
                    </label>
                    <label class="btn btn-outline-success">
                      <input type="radio" name="level" value="gerente" autocomplete="off"> Gerente
                    </label>
                    <label class="btn btn-outline-success">
                      <input type="radio" name="level" value="admin" autocomplete="off"> Admin
                    </label>
                  </div>
                </div>
              </div>
              <div class="">
                <div class="font-weight-bold my-2">
                  Permissões senhaunica
                </div>
                <div class="permissoes-senhaunica ml-2"> </div>
              </div>
            </div>

          </div>
          <div class="mt-3">
            <div class="float-right">
              <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-sm btn-primary">Salvar</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</div>

@section('javascripts_bottom')
  @parent
  <script>
    $(document).ready(function() {

      var senhaunicaUserPermission = $('#senhaunica-users-permission-modal')

      $('.senhaunicaUserPermissionBtn').unbind().on('click', function() {

        var permissaoApp = '';
        $.get("{{ route('senhaunica.listarPermissoesAplicacao') }}", function(permissions) {
          console.log(permissions)
          permissions.forEach(function(permission) {
            permissaoApp += permission.name + ', '
          })
          if (permissaoApp) {
            senhaunicaUserPermission
              .find('.permissao-app')
              .html(permissaoApp.substring(0, permissaoApp.length - 2))
          }
        })

        $.get($(this).data('route'), function(user) {
          var userPermissionString = '';
          console.log(user)
          user.permissions.forEach(function(permission) {
            if (permission.guard_name == 'senhaunica') {
              userPermissionString += permission.name + ', '
            }
          })
          senhaunicaUserPermission
            .find('.permissoes-senhaunica')
            .html(userPermissionString.substring(0, userPermissionString.length - 2))

          senhaunicaUserPermission.find('.name').html(user.name)
          senhaunicaUserPermission.modal();

        })

        // console.log(userId)
      })



      // coloca o focus no select2
      // https://stackoverflow.com/questions/25882999/set-focus-to-search-text-field-when-we-click-on-select-2-drop-down
      //   $(document).on('select2:open', () => {
      //     document.querySelector('.select2-search__field').focus();
      //   });

      //   $oSelect2.select2({
      //     ajax: {
      //       url: dataAjax,
      //       dataType: 'json',
      //       delay: 1000
      //     },
      //     dropdownParent: senhaunicaUserModal,
      //     minimumInputLength: 4,
      //     theme: 'bootstrap4',
      //     width: 'resolve',
      //     language: 'pt-BR'
      //   })

    })
  </script>
@endsection
