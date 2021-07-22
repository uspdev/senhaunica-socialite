@extends(config('senhaunica.template'))

@section('content')

<div class="h4">Senhaunica-socialite</div>

<div class="alert alert-danger mt-4">
  <div class="alert-heading h4">
    Serviço indisponível!
  </div>
  Inclua no seu arquivo App\Models\User o trait da senhaunica:
  <div class="ml-3">use \Uspdev\SenhaunicaSocialite\Traits\HasSenhaunica;</div>
</div>


@endsection

