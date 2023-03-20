@foreach ($user->roles->where('guard_name', 'app')->all() as $r)
<button data-user-id="{{ $user->id }}" data-route="{{ route('senhaunica-users.show', $user->id) }}"
  class="btn btn-sm btn-secondary py-0 senhaunicaUserPermissionBtn" title="Funções">
  {{ $r->name }}
</button>
@endforeach
