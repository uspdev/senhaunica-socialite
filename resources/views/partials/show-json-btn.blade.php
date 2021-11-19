@if ($json = $user->hasSenhaunicaJson())
  <button class="btn btn-sm btn-outline-secondary px-2 py-0" onclick="openJsonModal({{ $user->id }})">
    <i class="far fa-file-code"></i>
  </button>
@else
  -
@endif

@once
  @section('bottom_senhaunica_users')
    @parent
    <div class="modal fade" id="jsonModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
        </div>
      </div>
    </div>
  @endsection

  @section('javascripts_bottom')
    @parent
    <script>
      var openJsonModal = function(id) {
        var url = '{{ route('SenhaunicaGetJsonModalContent', ['id' => '_id_']) }}'
        url = url.replace('_id_', id)
        $('#jsonModal .modal-content').html('');
        $('#jsonModal .modal-content').load(url);
        $('#jsonModal').modal()
        return false;
      }
    </script>
  @endsection

@endonce
