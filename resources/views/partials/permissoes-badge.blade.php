<span class="badge badge-{{ $user->labelLevel() }}">
  {{ $user->level }}
  {{ $user->env ? '(env)' : '' }}
</span>
