  @foreach ($user->permissions->where('guard_name', App\Models\User::$vinculoNs) as $p)
    {{-- {{ $p->name }} --}}
    @if (in_array(explode('.', $p->name)[0], App\Models\User::$permissoesVinculo))
      <button data-user-id="{{ $user->id }}" data-route="{{ route(config('senhaunica.userRoutes') . '.show', $user->id) }}"
        class="btn btn-sm btn-primary py-0 senhaunicaUserPermissionBtn" title="Vínculos">
        {{ $p->name }}
      </button>
    @endif
  @endforeach
