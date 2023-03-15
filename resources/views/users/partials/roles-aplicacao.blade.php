<div class="font-weight-bold mb-2">
  Funções (roles) da aplicação<br>
</div>
<div class="role-app ml-2">

  @forelse ($rolesAplicacao as $p)
    <div class="form-check">
      <label class="form-check-label">
        <input type="checkbox" class="form-check-input" name="role_app[]" value="{{ $p->name }}">
        {{ $p->name }}
      </label>
    </div>
  @empty
    Sem funções (roles) disponíveis
  @endforelse
</div>
