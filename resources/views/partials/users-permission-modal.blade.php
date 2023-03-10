<button data-user-id="{{ $user->id }}" data-route="{{ route('senhaunica-users.show', $user->id) }}"
  class="btn btn-sm btn-outline-primary senhaunicaUserPermissionBtn">
  @include('senhaunica::partials.permissoes-badges')
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
                @forelse ($permissoesAplicacao as $p)
                  <div class="form-check">
                    <label class="form-check-label">
                      <input type="checkbox" class="form-check-input" name="permission_app[]"
                        value="{{ $p->name }}">
                      {{ $p->name }}
                    </label>
                  </div>
                @empty
                  Sem permissões disponíveis
                @endforelse
              </div>
            </div>
            <div class="col-md-6">

              <div class="">
                <div class="font-weight-bold mb-2">
                  Permissões hierárquicas
                </div>
                <div class="ml-2 permissao-hierarquica">
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
                  Permissões de vínculo
                </div>
                <div class="permissoes-vinculo ml-2">
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

        // pega do servidor dados do model User, incluindo permissões
        $.get(route, function(user) {
          var userPermissionString = ''
          console.log(user)
          // limpando os checkbox
          senhaunicaUserPermission.find('.permissao-app').find('input').attr('checked', false)

          user.permissions.forEach(function(permission) {
            // formatando string com permissões de vinculo
            if (permission.guard_name == 'senhaunica') {
              userPermissionString += permission.name + ', '
            }

            // permissoes hierarquicas
            if (permission.guard_name == 'web') {
              if (user.env) {
                senhaunicaUserPermission.find('.permissao-hierarquica').html(
                    '<span class="badge"></span>'+user.env + ' (env)</span>')
              } else {
                senhaunicaUserPermission.find('input[value=' + permission.name + ']').click()
              }
            }
            //tem de ajustar aqui para pegar o mais alto

            // permissoes do app: ticando o checkbox
            if (permission.guard_name == 'app') {
              senhaunicaUserPermission
                .find('input[value="' + permission.name + '"]').attr('checked', true)
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
          senhaunicaUserPermission.modal();

        })
      })
    })
  </script>
@endsection
