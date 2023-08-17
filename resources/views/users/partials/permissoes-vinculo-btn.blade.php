  @foreach ($user->permissions->where('guard_name', App\Models\User::$vinculoNs)->whereIn('name', App\Models\User::$permissoesVinculo) as $p)
    <button data-user-id="{{ $user->id }}" data-route="{{ route('senhaunica-users.show', $user->id) }}"
      class="btn btn-sm btn-primary py-0 senhaunicaUserPermissionBtn" title="Vínculos">
      {{ $p->name }}
    </button>
  @endforeach
