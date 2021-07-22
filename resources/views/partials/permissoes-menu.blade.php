@if (!$user->isManagedByEnv())
  <button class="btn btn-sm btn-outline-primary dropdown-toggle px-2 py-0" type="button"
    data-toggle="dropdown"></button>

  <div class="dropdown-menu dropdown-menu-right text-right">
    <form method="post" action="{{ route('SenhaunicaUpdatePermission', ['id' => $user->id]) }}">
      @csrf
      @foreach ($user->getPermissionsToChange() as $p)
        <button class="dropdown-item" type="submit" name="level" value="{{ $p['value'] }}" {{ $p['disabled'] }}>
          {{ $p['text'] }}
        </button>
      @endforeach
    </form>
  </div>
@endif
