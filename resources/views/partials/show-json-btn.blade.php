@if ($json = $user->hasSenhaunicaJson())
  <button class="btn btn-sm btn-outline-secondary px-2 py-0" onclick="openJsonModal({{ $user->id }})">
    <i class="far fa-file-code"></i>
  </button>

  {{-- colocando o modal fora da tabela para funcionar em mobile --}}

@else
  -
@endif
