@section('styles')
  @parent
  <style>
    .col-permission {
      text-align: right;
      width: 130px;
    }

    .col-button {
      width: 30px;
      text-align: center;
    }
  </style>
@endsection

<table class="table table-bordered table-hover table-sm mt-3">
  <thead>
    <tr>
      @foreach ($columns as $column)
        <th>{{ $column['text'] }}</th>
      @endforeach

      @if (config('senhaunica.permission'))
        <th>Permissões</th>
      @endif

      @if (config('senhaunica.customUserField.view'))
        <th style="width: {{ config('senhaunica.customUserField.width') }}">
          {{ config('senhaunica.customUserField.label') }}</th>
      @endif
      @if (config('senhaunica.destroyUser'))
        <th class="px-1">Remover</th>
      @endif
      <th class="px-1">Json</th>
      <th class="px-1">
        <span class="d-xs-inline d-sm-none">Assumir identidade</span> {{-- aparecerá somente em mobile --}}
        <span class="d-none d-sm-inline">Ident.</span> {{-- aparecerá nas demais telas --}}
      </th>

    </tr>
  </thead>
  <tbody>
    @forelse ($users as $user)
      <tr class="data-row">
        @foreach ($columns as $column)
          <td>{{ $user->{$column['key']} }}</td>
        @endforeach

        @if (config('senhaunica.permission'))
          <td>
            @include('senhaunica::partials.users-permission-modal')
          </td>
        @endif

        @if (config('senhaunica.customUserField.view'))
          <td class="">@include(config('senhaunica.customUserField.view'))</td>
        @endif
        @if (config('senhaunica.destroyUser'))
          <td class="col-button">
            @include('senhaunica::partials.destroy-user-btn')
          </td>
        @endif
        <td class="col-button">
          @include('senhaunica::partials.show-json-btn')
        </td>
        <td class="col-button">
          @include('senhaunica::partials.assumir-identidade-btn')
        </td>

      </tr>
    @empty
      <tr class="data-row">
        <td colspan="7" class="text-center">Nenhum registro encontrado</td>
      </tr>
    @endforelse
  </tbody>
</table>
@if (isset($search))
  {{ $users->appends($search)->links() }}
@else
  {{ $users->links() }}
@endif


@section('javascripts_bottom')
  @parent
  <script>
    $(document).ready(function() {

      $('[data-toggle="tooltip"]').tooltip({
        placement: 'auto'
      })

    })
  </script>
@endsection

@yield('bottom_senhaunica_users')
