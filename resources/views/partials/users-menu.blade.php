<div class="form-inline">
  <span class="h4">
    <span class="d-none d-sm-inline">Senhaunica-socialite</span>
    <span class="d-xs-inline d-sm-none">SS</span>
    <i class="fas fa-angle-right"></i> Usuários
  </span>
  <span class="ml-3">
    @if (hasReplicado())
      <span class="badge badge-success" data-toggle="tooltip" title="Usando replicado">
        replicado
      </span>
    @else
      <span class="badge badge-secondary" data-toggle="tooltip" title="Replicado indisponível">
        <s>replicado</s>
      </span>
    @endif

    @if (config('senhaunica.permission'))
      <span class="badge badge-success" data-toggle="tooltip" title="Usando permissões internas">
        permission
      </span>
    @else
      <span class="badge badge-secondary" data-toggle="tooltip" title="Permissões internas desativadas">
        <s>permission</s>
      </span>
    @endif

    @if (config('senhaunica.dropPermissions'))
      <span class="badge badge-warning" data-toggle="tooltip" title="Permissões gerenciadas pelo .env">
        drop <span class="d-none d-sm-inline">permissions</span>
      </span>
    @else
      <span class="badge badge-secondary" data-toggle="tooltip" title="Permissões gerenciadas pela aplicação">
        <s>drop <span class="d-none d-sm-inline">permissions</span></s>
      </span>
    @endif

    @if (config('senhaunica.onlyLocalUsers'))
      <span class="badge badge-warning" data-toggle="tooltip" title="Somente usuários locais">
        Local User
      </span>
    @else
      <span class="badge badge-secondary" data-toggle="tooltip" title="Qualquer usuário pode logar">
        <s>Local User</s>
      </span>
    @endif

    @if (config('senhaunica.destroyUser'))
      <span class="badge badge-warning" data-toggle="tooltip" title="Remover usuário habilitado">
        destroy
      </span>
    @else
      <span class="badge badge-secondary" data-toggle="tooltip" title="Remover usuário indisponível">
        <s>destroy</s>
      </span>
    @endif

    @if (config('senhaunica.debug'))
      <span class="badge badge-danger" data-toggle="tooltip" title="Modo debug habilitado">debug</span>
    @else
      <span class="badge badge-secondary" data-toggle="tooltip" title="Modo debug desativado"><s>debug</s></span>
    @endif
    @if (config('senhaunica.dev') != 'no')
      <span class="badge badge-danger" data-toggle="tooltip" title="{{ config('senhaunica.dev') }}">dev</span>
    @else
      <span class="badge badge-primary" data-toggle="tooltip" title="Oauth em ambiente de produção">prod</span>
    @endif
  </span>
</div>
@if ($errors->any())
  <div class="alert alert-danger">
    <ul>
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
<div class="form-inline mt-2">
  @include('senhaunica::partials.filterbox')
  @include('senhaunica::partials.users-add')
</div>
