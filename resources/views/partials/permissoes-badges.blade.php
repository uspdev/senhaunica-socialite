@if ($user->can('admin'))
  <span class="badge badge-danger">
    admin
    @if (in_array($user->codpes, config('senhaunica.admins')))
      (env)
    @endif
  </span>
@endif
@if ($user->can('gerente') && $user->cannot('admin'))
  <span class="badge badge-warning">
    gerente
    @if (in_array($user->codpes, config('senhaunica.gerentes')))
      (env)
    @endif
  </span>
@endif
@if ($user->can('user') && $user->cannot('admin') && $user->cannot('gerente'))
  <span class="badge badge-success">
    usuário
  </span>
@endif