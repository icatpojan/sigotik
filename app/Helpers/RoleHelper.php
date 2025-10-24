<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use App\Models\ConfRoleMenu;
use App\Models\StmMenuv2;

class RoleHelper
{
    /**
     * Check if current user has permission for a specific menu
     *
     * @param string $menuRoute
     * @return bool
     */
    public static function hasPermission($menuRoute)
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();

        // Cari menu berdasarkan route name atau linka
        $menu = StmMenuv2::where('linka', $menuRoute)
            ->orWhere('linka', 'like', '%' . $menuRoute . '%')
            ->first();

        if (!$menu) {
            // Jika menu tidak ditemukan di database, deny access
            return false;
        }

        // Cek apakah user memiliki permission untuk menu ini
        return ConfRoleMenu::where('conf_group_id', $user->conf_group_id)
            ->where('stm_menu_id', $menu->id)
            ->exists();
    }

    /**
     * Get user's accessible menus
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getUserMenus()
    {
        if (!Auth::check()) {
            return collect();
        }

        $user = Auth::user();

        return StmMenuv2::whereHas('groups', function ($query) use ($user) {
            $query->where('conf_group_id', $user->conf_group_id);
        })
            ->orderBy('urutan')
            ->get();
    }

    /**
     * Get user's accessible parent menus (level 1)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getUserParentMenus()
    {
        if (!Auth::check()) {
            return collect();
        }

        $user = Auth::user();

        return StmMenuv2::whereHas('groups', function ($query) use ($user) {
            $query->where('conf_group_id', $user->conf_group_id);
        })
            ->where('level', 1)
            ->orderBy('urutan')
            ->get();
    }

    /**
     * Get user's accessible child menus for a specific parent
     *
     * @param int $parentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getUserChildMenus($parentId)
    {
        if (!Auth::check()) {
            return collect();
        }

        $user = Auth::user();

        return StmMenuv2::whereHas('groups', function ($query) use ($user) {
            $query->where('conf_group_id', $user->conf_group_id);
        })
            ->where('id_parentmenu', $parentId)
            ->orderBy('urutan')
            ->get();
    }

    /**
     * Check if user has specific role
     *
     * @param int $groupId
     * @return bool
     */
    public static function hasRole($groupId)
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->conf_group_id == $groupId;
    }

    /**
     * Get current user's role name
     *
     * @return string|null
     */
    public static function getCurrentRoleName()
    {
        if (!Auth::check()) {
            return null;
        }

        $user = Auth::user();
        return $user->group ? $user->group->group : null;
    }

    /**
     * Check if user has access to any submenu of a parent menu.
     *
     * @param array $submenuRoutes Array of route names for submenus
     * @return bool
     */
    public static function hasAnySubmenuAccess(array $submenuRoutes): bool
    {
        foreach ($submenuRoutes as $route) {
            if (self::hasPermission($route)) {
                return true;
            }
        }
        return false;
    }
}
