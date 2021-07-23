<button class="btn btn-sm btn-outline-primary senhaunicaUseraddBtn" @if (!hasReplicado()) disabled title="Necessário Replicado para adicionar usuários" @endif>
  <i class="fas fa-plus"></i> Adicionar
</button>

<div class="modal fade" id="senhaunica-socialite-adicionar-pessoas-modal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">Adicionar Pessoas</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <div class="">
          <form class="" method="POST" action="{{ route('users.store') }}" data-ajax="{{ config('senhaunica.userRoutes') }}/find">
            @csrf
            <div class="row mb-3">
              <div class="col-md-8">
                <select name="codpes" class="form-control form-control-sm">
                  <option>Digite o nome ou codpes..</option>
                </select>
              </div>
              <div class="col-md-4">
                <div class="btn-group btn-group-toggle float-right" data-toggle="buttons">
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

            <div class="float-right">
              <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-sm btn-primary">Salvar</button>
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

      var senhaunicaUserModal = $('#senhaunica-socialite-adicionar-pessoas-modal')
      var $oSelect2 = senhaunicaUserModal.find(':input[name=codpes]')
      var dataAjax = senhaunicaUserModal.find('form').data('ajax')

      $('.senhaunicaUseraddBtn').on('click', function() {
        senhaunicaUserModal.modal();
      })

      // abre o select2 automaticamente
      senhaunicaUserModal.on('shown.bs.modal', function() {
        $oSelect2.select2('open')
      })

      // coloca o focus no select2
      // https://stackoverflow.com/questions/25882999/set-focus-to-search-text-field-when-we-click-on-select-2-drop-down
      $(document).on('select2:open', () => {
        document.querySelector('.select2-search__field').focus();
      });

      $oSelect2.select2({
        ajax: {
          url: dataAjax,
          dataType: 'json',
          delay: 1000
        },
        dropdownParent: senhaunicaUserModal,
        minimumInputLength: 4,
        theme: 'bootstrap4',
        width: 'resolve',
        language: 'pt-BR'
      })

    })
  </script>
@endsection
