@foreach ($user->getAllPermissions()->where('guard_name', App\Models\User::$appNs) as $p)
  <button data-user-id="{{ $user->id }}" data-route="{{ route(config('senhaunica.userRoutes') . '.show', $user->id) }}"
    class="btn btn-sm btn-info py-0 senhaunicaUserPermissionBtn" title="Permissões">
    {{ $p->name }}
  </button>
@endforeach
