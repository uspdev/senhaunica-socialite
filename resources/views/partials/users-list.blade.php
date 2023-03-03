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

      <th>Permissões</th>

      @if (config('senhaunica.customUserField.view'))
        <th style="width: {{ config('senhaunica.customUserField.width') }}">{{ config('senhaunica.customUserField.label') }}</th>
      @endif
      @if (config('senhaunica.destroyUser'))
        <th class="px-1">Remover</th>
      @endif
      <th class="px-1">Json</th>
      <th class="px-1">
        <span class="d-xs-inline d-sm-none">Assumir identidade</span> {{-- aparecerá somente em mobile --}}
        <span class="d-none d-sm-inline">Ident.</span> {{-- aparecerá nas demais telas --}}
      </th>
      @if (config('senhaunica.permission'))
        <th>Permissões</th>
      @endif
    </tr>
  </thead>
  <tbody>
    @forelse ($users as $user)
      <tr class="data-row">
        @foreach ($columns as $column)
          <td>{{ $user->{$column['key']} }}</td>
        @endforeach

        <td>{{ $user->categorias() }}</td>

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
        @if (config('senhaunica.permission'))
          <td class="col-permission">
            <div class="clearfix">
              <div class="float-left">
                @include('senhaunica::partials.permissoes-badges')
              </div>
              <div class="float-right">
                @includewhen(!config('senhaunica.dropPermissions'),'senhaunica::partials.permissoes-menu')
              </div>
            </div>
          </td>
        @endif
      </tr>
    @empty
      <tr class="data-row">
        <td colspan="7" class="text-center">Nenhum registro encontrado</td>
      </tr>
    @endforelse
  </tbody>
</table>
@if(isset($search))
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
