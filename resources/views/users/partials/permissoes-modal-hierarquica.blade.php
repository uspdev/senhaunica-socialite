<div class="font-weight-bold mb-2">
  Permissões hierárquicas
</div>
<div class="ml-2 permissao-hierarquica">
  @foreach (App\Models\User::$permissoesHierarquia as $p)
    <div class="form-check">
      <label class="form-check-label">
        <input type="radio" class="form-check-input" name="level" value="{{ $p }}">
        <span class="text-{{ isset($user) ? $user->labelLevel($p) : "" }}">{{ $p }}</span>
      </label>
    </div>
  @endforeach
</div>
