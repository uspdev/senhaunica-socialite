@foreach ($user->roles->all() as $r)
<button data-user-id="{{ $user->id }}" data-route="{{ route(config('senhaunica.userRoutes') . '.show', $user->id) }}"
  class="btn btn-sm btn-secondary py-0 senhaunicaUserPermissionBtn" title="Funções">
  {{ $r->name }}
</button>
@endforeach
