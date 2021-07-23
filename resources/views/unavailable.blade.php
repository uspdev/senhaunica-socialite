@extends(config('senhaunica.template'))

@section('content')

  <div class="h4">Senhaunica-socialite</div>

  @if ($reason == 'noLocalUser')
    <div class="alert alert-danger mt-4">
      <div class="alert-heading h4">
        Usuário sem acesso!
      </div>
      Seu usuário não está autorizado nesse sistema!<br>
      <a href="">Retorne ao início</a>

    </div>
  @else
    <div class="alert alert-danger mt-4">
      <div class="alert-heading h4">
        Serviço indisponível!
      </div>
      Inclua no seu arquivo App\Models\User o trait da senhaunica:
      <div class="ml-3">use \Uspdev\SenhaunicaSocialite\Traits\HasSenhaunica;</div>
    </div>
  @endif

@endsection
