@extends(config('senhaunica.template'))

@section('content')

<div class="mx-auto" style="width: 500px">
  <h4>Senhaunica-socialite <i class="fas fa-angle-right"></i> Login</h4>
  @if ($errors->any())
      <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
  @endif
  <form method="POST" action="{{ route('SenhaunicaLocalLoginAs') }}">
    @csrf
    <div class="form-group mt-4">
      <label for="email">Email</label>
      <input type="email" class="form-control" name="email" placeholder="Email">
    </div>
    <div class="form-group">
      <label for="password">Senha</label>
      <input type="password" class="form-control" name="password" placeholder="Senha">
    </div>
    <button type="submit" class="btn btn-primary">Entrar</button>
  </form>
</div>

@endsection
