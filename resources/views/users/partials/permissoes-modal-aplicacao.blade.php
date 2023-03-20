<div class="font-weight-bold mb-2">
  Permissões da aplicação<br>
</div>
<div class="permissao-app ml-2">
  @forelse ($permissoesAplicacao as $p)
    <div class="form-check">
      <label class="form-check-label">
        <input type="checkbox" class="form-check-input" name="permission_app[]" value="{{ $p->name }}">
        {{ $p->name }}
      </label>
    </div>
  @empty
    Sem permissões disponíveis
  @endforelse
</div>
