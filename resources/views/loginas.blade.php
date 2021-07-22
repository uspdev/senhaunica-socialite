@extends(config('senhaunica.template'))

@section('content')

  <h4>Senhaunica-socialite <i class="fas fa-angle-right"></i> Login as</h4>
  <br>
  <form class="" method="POST" action="{{ route('SenhaunicaLoginAs') }}">
    @csrf
    <div class="form-group row">
      <label for="codpes" class="col-auto col-form-label text-md-right">Número Usp</label>
      <div class="col-md-3">
        <input class="form-control @error('codpes')is-invalid @enderror" type="text" name="codpes"
          value="{{ old('codpes') }}" required autofocus>
        @error('codpes')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="col-auto">
        <button type="submit" class="btn btn-primary">Entrar</button>
      </div>
    </div>
  </form>

@endsection
