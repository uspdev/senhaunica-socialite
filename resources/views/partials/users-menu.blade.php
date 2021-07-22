<div class="form-inline">
  <span class="h4 align-middle"><span class="d-none d-sm-inline">Senhaunica-socialite</span><span class="d-xs-inline d-sm-none">SS</span> <i class="fas fa-angle-right"></i> Usuários</span>
  @include('senhaunica::partials.filterbox')
  <span class="">
    @if (class_exists('Uspdev\\Replicado\\Pessoa'))
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
        drop permissions
      </span>
    @else
      <span class="badge badge-secondary" data-toggle="tooltip" title="Permissões gerenciadas pela aplicação">
        <s>drop permissions</s>
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
