<form method="POST" action="{{ route('SenhaunicaLoginAs') }}">
  @csrf
  <input type="hidden" name="codpes" value="{{ $user->codpes }}">
  <button type="submit" class="btn btn-outline-secondary px-1 py-0" data-toggle="tooltip" title="Assumir identidade">
    <i class="fas fa-user-secret"></i>
  </button>
</form>
