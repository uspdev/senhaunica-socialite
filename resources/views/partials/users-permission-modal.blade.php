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
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="" method="POST" action="">
          @csrf
          @method('PUT')
          <div class="row">
            <div class="col-md-6">
              <div class="font-weight-bold mb-2">
                Permissões da aplicação<br>
              </div>
              <div class="permissao-app ml-2">
                {{-- este conteúdo será substituído pelo JS --}}
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
                    <label class="btn btn-outline-success">
                      <input type="radio" name="level" value="user" autocomplete="off"> Usuário
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
                <div class="permissoes-senhaunica ml-2">
                  {{-- este conteúdo será substituído pelo JS --}}
                </div>
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

        var route = $(this).data('route')
        // pega do servidor as permissões do app
        var permissaoApp = '';
        var checkbox =
          '<div class="form-check"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="permission_app[]" value="permName">permName</label></div>'
        $.get("{{ route('senhaunica.listarPermissoesAplicacao') }}", function(permissions) {
          console.log(permissions)
          permissions.forEach(function(permission) {
            permissaoApp += checkbox.replace(/permName/g, permission.name)
          })
          if (permissaoApp) {
            senhaunicaUserPermission
              .find('.permissao-app')
              .html(permissaoApp)
          }
        })

        // pega do servidor dados do model User, incluindo permissões
        $.get(route, function(user) {
          var userPermissionString = '';
          console.log(user)

          user.permissions.forEach(function(permission) {
            // formatando permissões do senhaunica
            if (permission.guard_name == 'senhaunica') {
              userPermissionString += permission.name + ', '
            }

            if(permission.name == 'admin' && permission.guard_name == 'web') {
                senhaunicaUserPermission.find('input[value="admin"]').attr('checked', true)
            }

            if (permission.guard_name == 'app') {
                senhaunicaUserPermission.find('input[value="'+permission.name+'"]').attr('checked', true)
            }

          })
          senhaunicaUserPermission
            .find('.permissoes-senhaunica')
            .html(userPermissionString.substring(0, userPermissionString.length - 2))

          // preenchendo form para hierárquicas


          senhaunicaUserPermission.find('form').attr('action', route)
          console.log(route)
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
