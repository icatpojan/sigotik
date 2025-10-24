<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ConfRoleMenu;
use App\Models\StmMenuv2;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $menuRoute
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $menuRoute = null)
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Jika tidak ada menuRoute yang dikirim, skip role check
        if (!$menuRoute) {
            return $next($request);
        }

        // Cari menu berdasarkan route name
        $menu = StmMenuv2::where('linka', $menuRoute)->first();

        if (!$menu) {
            // Jika menu tidak ditemukan, cek berdasarkan route name pattern
            $menu = StmMenuv2::where('linka', 'like', '%' . $menuRoute . '%')->first();
        }

        if (!$menu) {
            // Jika menu tidak ditemukan, allow access (untuk menu yang belum ada di database)
            return $next($request);
        }

        // Cek apakah user memiliki permission untuk menu ini
        $hasPermission = ConfRoleMenu::where('conf_group_id', $user->conf_group_id)
            ->where('stm_menu_id', $menu->id)
            ->exists();

        if (!$hasPermission) {
            // Jika tidak ada permission, redirect ke dashboard dengan error
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
