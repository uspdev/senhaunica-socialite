@if ($user->getAllPermissions()->where('guard_name', 'app')->all())
  @foreach ($user->getAllPermissions()->where('guard_name', 'app') as $p)
    <button data-user-id="{{ $user->id }}" data-route="{{ route('senhaunica-users.show', $user->id) }}"
      class="btn btn-sm btn-info py-0 senhaunicaUserPermissionBtn" title="Permissões">
      {{ $p->name }}
    </button>
  @endforeach
@endif
