@if ($user->hasPermissionTo('admin', 'web'))
  <span class="badge badge-danger">
    admin
    @if ($user->env)
      (env)
    @endif
  </span>
@endif
@if ($user->hasPermissionTo('gerente', 'web') || $user->hasPermissionTo('manager', 'web'))
  <span class="badge badge-warning">
    gerente
    @if ($user->env)
      (env)
    @endif
  </span>
@endif
@if ($user->hasPermissionTo('user', 'web'))
  <span class="badge badge-success">
    usuário
  </span>
@endif
