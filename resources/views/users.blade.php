@extends(config('senhaunica.template'))

@section('content')
  @include('senhaunica::partials.users-menu')
  @include('senhaunica::partials.users-list')

  {{-- <h4>Todas as permissões</h4>
  {!! \App\Models\User::listarTodasPermissoes() !!} --}}
@endsection
