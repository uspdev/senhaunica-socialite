@extends(config('senhaunica.template'))

@section('content')

  @php($reason = $reason ?? null)

  @if ($reason == 'authServerDown')
    <div class="alert alert-warning mt-4">
      <div class="alert-heading h4">
        Serviço de autenticação indisponível
      </div>
      O serviço de autenticação da Universidade de São Paulo (Senha Única) está indisponível neste momento.
      Tente novamente mais tarde.<br>
      <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm mt-3">Tentar novamente</a>
    </div>
  @elseif ($reason == 'noLocalUser')
    <div class="h4">Senhaunica-socialite</div>
    <div class="alert alert-danger mt-4">
      <div class="alert-heading h4">
        Usuário sem acesso!
      </div>
      Seu usuário não está autorizado nesse sistema!<br>
      <a href="">Retorne ao início</a>

    </div>
  @else
    <div class="h4">Senhaunica-socialite</div>
    <div class="alert alert-danger mt-4">
      <div class="alert-heading h4">
        Serviço indisponível!
      </div>
      Inclua no seu arquivo App\Models\User o trait da senhaunica:
      <div class="ml-3">use \Uspdev\SenhaunicaSocialite\Traits\HasSenhaunica;</div>
    </div>
  @endif

@endsection
