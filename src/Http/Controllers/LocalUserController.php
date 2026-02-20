<?php

namespace Uspdev\SenhaunicaSocialite\Http\Controllers;

use App\Models\User;
use Uspdev\SenhaunicaSocialite\Http\Requests\LocalUserRequest;
use Uspdev\SenhaunicaSocialite\Http\Requests\LocalUserUpdateRequest;

class LocalUserController extends Controller
{
    /**
     * Cria usuário local
     */
    public function store(LocalUserRequest $request)
    {
        $this->authorize('admin');

        $user = User::findOrCreateLocalUser($request->validated());

        if (!($user instanceof \App\Models\User)) {
            return redirect()->back()->withErrors($user)->withInput();
        }

        return back()->with('alert-info', 'Usuário criado com sucesso. Você pode editar as permissões dele clicando em alguma permissão!');
    }

    public function edit($id)
    {
        $this->authorize('admin');

        $user = User::find($id);

        return response()->json($user);
    }

    public function update(LocalUserUpdateRequest $request, $id)
    {
        $this->authorize('admin');

        $user = User::findOrUpdateLocalUser($request->validated() + ['id' => $id]);

        if (!($user instanceof \App\Models\User)) {
            return redirect()->back()->withErrors($user)->withInput();
        }

        return back()->with('alert-info', 'Usuário atualizado com sucesso!');
    }
}
