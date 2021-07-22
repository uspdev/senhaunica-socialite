<div class="modal-header">
  <h5 class="modal-title">{{ $user->codpes }} {{ $user->name }} ({{ $date }})</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body">
  <pre>{{ $json }}</pre>
</div>
