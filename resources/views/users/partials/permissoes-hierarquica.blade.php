<div class="font-weight-bold mb-2">
  Permissões hierárquicas
</div>
<div class="ml-2 permissao-hierarquica">
  @foreach ($permissoesHierarquica as $p)
    <div class="form-check">
      <label class="form-check-label">
        <input type="radio" class="form-check-input" name="level" value="{{ $p->name }}">
        {{ $p->name }}
      </label>
    </div>
  @endforeach
</div>
