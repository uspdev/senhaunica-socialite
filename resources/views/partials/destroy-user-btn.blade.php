@if (!$user->env)
  <form method="POST" action="{{ url(config('senhaunica.userRoutes')) }}/{{ $user->id }}">
    @method('DELETE')
    @csrf
    <button class="btn btn-sm btn-outline-danger py-0 px-2" onclick="return confirm('Tem certeza?')"
      data-toggle="tooltip" title="Remover usuário">
      <i class="fas fa-ban"></i>
    </button>
  </form>
@else
  <div data-toggle="tooltip" title="Usuário gerenciado pelo env">
    <button class="btn btn-sm btn-outline-secondary py-0 px-2" style="pointer-events: none;" disabled>
      <i class="fas fa-ban"></i>
    </button>
  </div>
@endif
