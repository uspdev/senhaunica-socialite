@extends(config('senhaunica.template'))

@section('content')
  @include('senhaunica::partials.users-menu')
  @include('senhaunica::partials.users-list')
@endsection
