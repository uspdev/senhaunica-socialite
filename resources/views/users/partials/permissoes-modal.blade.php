

@section('javascripts_bottom')
  @parent
  @once
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
                <div class="col-md-4">
                  @include('senhaunica::users.partials.permissoes-modal-aplicacao')
                </div>
                <div class="col-md-4">
                  @include('senhaunica::users.partials.permissoes-modal-roles')
                </div>
                <div class="col-md-4">
                  <div class="">
                    @include('senhaunica::users.partials.permissoes-modal-hierarquica')
                  </div>
                  <div class="">
                    @include('senhaunica::users.partials.permissoes-modal-vinculo')
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
  @endonce
@endsection

@section('javascripts_bottom')
  @parent
  @once
    <script>
      $(document).ready(function() {

        var senhaunicaUserPermission = $('#senhaunica-users-permission-modal')
        var hierarquica = senhaunicaUserPermission.find('.permissao-hierarquica').html()

        $('.senhaunicaUserPermissionBtn').unbind().on('click', function() {

          var route = $(this).data('route')

          // pega do servidor dados do model User, incluindo permissões
          $.get(route, function(user) {
            var userPermissionString = ''
            console.log(user)
            // limpando os checkbox
            senhaunicaUserPermission.find('.permissao-app').find('input').attr('checked', false)
            senhaunicaUserPermission.find('.role-app').find('input').attr('checked', false)

            user.permissions.forEach(function(permission) {
              // formatando string com permissões de vinculo
              if (permission.guard_name == user.vinculoNs && user.permissoesVinculo.includes(permission.name.split('.')[0]) ) {
                userPermissionString += permission.name + ', '
              }

              // permissoes hierarquicas
              if (user.permissoesHierarquia.includes(permission.name) && permission.guard_name == '{{ App\Models\User::$hierarquiaNs }}') {
                if (user.env) {
                  senhaunicaUserPermission.find('.permissao-hierarquica').html(
                    '<span class="badge badge-danger">' + user.env + ' (env)</span>')
                } else {
                  senhaunicaUserPermission.find('.permissao-hierarquica').html(hierarquica)
                  senhaunicaUserPermission
                    .find('.permissao-hierarquica input[value=' + permission.name + ']').click()
                }
              }
              //tem de ajustar aqui para pegar o mais alto

              // permissoes do app: ticando o checkbox
              if (permission.guard_name == '{{ App\Models\User::$appNs }}') {
                senhaunicaUserPermission
                  .find('.permissao-app input[value="' + permission.name + '"]').attr('checked', true)
              }
            })

            user.roles.forEach(function(role) {
              // roles do app: ticando o checkbox
              if (role.guard_name == '{{ App\Models\User::$appNs }}') {
                senhaunicaUserPermission
                  .find('.role-app input[value="' + role.name + '"]').attr('checked', true)
              }
            })

            //mostrando a string das permissoes-vinculo
            senhaunicaUserPermission.find('.permissoes-vinculo')
              .html(userPermissionString.substring(0, userPermissionString.length - 2))

            // setando o action com a rota correta
            senhaunicaUserPermission.find('form').attr('action', route)
            // colocando o nome no topo do modal
            senhaunicaUserPermission.find('.name').html(user.name)
            // ativando modal
            senhaunicaUserPermission.modal()

          })
        })
      })
    </script>
  @endonce
@endsection
