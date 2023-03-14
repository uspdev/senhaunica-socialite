  @foreach ($user->permissions->where('guard_name', 'senhaunica') as $p)
    <span class="badge badge-primary">
      {{ $p->name }}
    </span>
  @endforeach
