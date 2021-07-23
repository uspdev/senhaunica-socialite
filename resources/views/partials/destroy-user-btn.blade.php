<form method="POST" action="{{ route('users.destroy', ['user' => $user]) }}">
  @method('DELETE')
  @csrf
  <input type="hidden" name="codpes" value="{{ $user->codpes }}">
  <button class="btn btn-sm btn-outline-danger py-0 px-2" onclick="return confirm('Tem certeza?')" data-toggle="tooltip"
    title="Remover usuário">
    <i class="fas fa-ban"></i>
  </button>
</form>
