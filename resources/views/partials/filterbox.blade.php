<form method="GET" action="{{ route(config('senhaunica.userRoutes') . '.index') }}">
  <div class="input-group col-6 col-sm-4 col-md-12">
    <div class="input-group-prepend">
      <a href="{{ route(config('senhaunica.userRoutes') . '.index') }}?filter=__none__&page=1" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-times"></i>
      </a>
    </div>
    <input class="form-control form-control-sm" type="text" name="filter" placeholder="Filtrar..." id="dt-search"
      value="{{ $params['filter'] ?? '' }}">
      <input type="hidden" name="sort" value="{{ $params['sort'] }}">
      <input type="hidden" name="direction" value="{{ $params['direction'] }}">
      <input type="hidden" name="page" value="1">
    <div class="input-group-append">
      <button type="submit" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-search"></i>
      </button>
    </div>
  </div>
</form>
