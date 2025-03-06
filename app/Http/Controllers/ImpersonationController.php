<?php

namespace App\Http\Controllers;

use App\Models\Scopes\TenantScope;
use App\Models\User;

class ImpersonationController extends Controller
{
    public function __invoke()
    {
        if(!session()->has('impersonate')) {
            abort(403);
        }
        // login as the super user in session
        auth()->login(User::withoutGlobalScope(TenantScope::class)->find(session('impersonate')));
        session()->forget('impersonate');

        return redirect('/dashboard');
    }
}
