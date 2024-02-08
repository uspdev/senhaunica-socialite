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
        <th>@sortablelink($column['key'], $column['text'])</th>
      @endforeach

      @if (!empty(config('senhaunica.customUserField')))
        @foreach (config('senhaunica.customUserField') as $cuf)
          <th style="width: {{ $cuf['width'] }}">
            @if (!empty($cuf['key']))
              @sortablelink($cuf['key'], $cuf['label'])
            @else
              {{ $cuf['label'] }}
            @endif

          </th>
        @endforeach
      @endif

      @if (config('senhaunica.permission'))
        <th colspan="4">Permissões (Hierárquico | Vínculo | Função | Aplicação)</th>
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
    @foreach ($users as $user)
      <tr class="data-row">
        @foreach ($columns as $column)
          <td>{{ $user->{$column['key']} }}</td>
        @endforeach

        @if (!empty(config('senhaunica.customUserField')))
          @foreach (config('senhaunica.customUserField') as $cuf)
            <td>@includeIf($cuf['view'])</td>
          @endforeach
        @endif

        @if (config('senhaunica.permission'))
          <td>@include('senhaunica::partials.permissoes-badge')</td>
          <td>@include('senhaunica::users.partials.permissoes-vinculo-btn')</td>
          <td>@include('senhaunica::users.partials.permissoes-funcao-btn')</td>
          <td>@include('senhaunica::users.partials.permissoes-aplicacao-btn')</td>
        @endif

        @if (config('senhaunica.destroyUser'))
          <td class="col-button">@include('senhaunica::partials.destroy-user-btn')</td>
        @endif

        <td class="col-button">@include('senhaunica::partials.show-json-btn')</td>
        <td class="col-button">@include('senhaunica::partials.assumir-identidade-btn')</td>
      </tr>
    @endforeach
  </tbody>
</table>

{{ $users->appends($params)->links() }}

@include('senhaunica::users.partials.permissoes-modal')

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
