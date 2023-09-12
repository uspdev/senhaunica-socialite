<form method="GET" action="{{ route('SenhaunicaSearchUsers') }}">
  @csrf
  <div class="input-group col-6 col-sm-4 col-md-12">
    <div class="input-group-prepend">
        <button class="btn btn-outline-secondary btn-sm" id="dt-search-clear">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <input class="form-control form-control-sm" type="text" name="filter"
      placeholder="Filtrar..." id="dt-search" value="{{ $search['filter'] ?? '' }}">
    <div class="input-group-append">
        <button class="btn btn-outline-secondary btn-sm" id="dt-search-button">
            <i class="fas fa-search"></i>
        </button>
    </div>
  </div>
</form>

@section('javascripts_bottom')
@parent
<script>
    $(document).ready(function() {

        $('#dt-search').focus();

        // vamos limpar o filtro de busca
        $('#dt-search-clear').on('click', function() {
            $('#dt-search').val('').trigger('keyup');
            $('#dt-search').focus();
        })

        $('#dt-search-button').on('keypress', function(e) {
            if(e.which === 13) {
                $('#dt-search-button').click();
            }
        })

    })
</script>
@endsection
