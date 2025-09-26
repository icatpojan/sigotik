<?php

namespace App\Http\Controllers;

use App\Models\StmMenuv2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('menus.index');
    }

    /**
     * Get menus data for DataTables
     */
    public function getMenus(Request $request)
    {
        Log::info('Menu search request:', $request->all());
        $query = StmMenuv2::with('parent');

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchValue = $request->search;
            $query->where(function ($q) use ($searchValue) {
                $q->where('menu', 'like', "%{$searchValue}%")
                    ->orWhere('linka', 'like', "%{$searchValue}%")
                    ->orWhere('icon', 'like', "%{$searchValue}%");
            });
        }

        // Filter by level
        if ($request->has('level') && $request->level !== '') {
            $query->where('level', $request->level);
        }

        // Filter by parent menu
        if ($request->has('parent_menu') && $request->parent_menu !== '') {
            if ($request->parent_menu === 'main') {
                $query->whereNull('id_parentmenu');
            } else {
                $query->where('id_parentmenu', $request->parent_menu);
            }
        }

        // Order by id descending (terbesar ke terkecil)
        $query->orderBy('id', 'desc');

        // Pagination
        $perPage = $request->get('length', 10);
        $page = ($request->get('start', 0) / $perPage) + 1;
        $menus = $query->paginate($perPage, ['*'], 'page', $page);

        Log::info('Menu query result:', [
            'total' => $menus->total(),
            'count' => count($menus->items()),
            'items' => $menus->items()
        ]);

        return response()->json([
            'draw' => intval($request->get('draw')),
            'recordsTotal' => StmMenuv2::count(),
            'recordsFiltered' => $menus->total(),
            'data' => $menus->items()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'menu' => 'required|string|max:255',
            'level' => 'required|integer|in:1,2',
            'id_parentmenu' => 'nullable|integer|exists:stm_menuv2,id',
            'linka' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'urutan' => 'required|integer|min:1'
        ], [
            'menu.required' => 'Nama menu harus diisi',
            'level.required' => 'Level menu harus dipilih',
            'level.in' => 'Level menu harus 1 (Menu Utama) atau 2 (Sub Menu)',
            'id_parentmenu.exists' => 'Parent menu tidak valid',
            'urutan.required' => 'Urutan menu harus diisi',
            'urutan.integer' => 'Urutan menu harus berupa angka',
            'urutan.min' => 'Urutan menu minimal 1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Generate auto-increment ID
            $maxId = StmMenuv2::max('id') ?? 0;
            $newId = $maxId + 1;

            // Validate business rules
            if ($request->level == 1 && $request->id_parentmenu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu Utama tidak boleh memiliki parent menu'
                ], 422);
            }

            if ($request->level == 2 && !$request->id_parentmenu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sub Menu harus memiliki parent menu'
                ], 422);
            }

            // Validate link requirement
            if ($request->level == 1 && !$request->id_parentmenu && !$request->linka) {
                // Main menu without sub menu needs link
                $hasChildren = StmMenuv2::where('id_parentmenu', $newId)->exists();
                if (!$hasChildren && !$request->linka) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Menu Utama tanpa Sub Menu harus memiliki link'
                    ], 422);
                }
            }

            if ($request->level == 2 && !$request->linka) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sub Menu harus memiliki link'
                ], 422);
            }

            // Validate icon requirement
            if ($request->level == 1 && !$request->icon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu Utama harus memiliki icon'
                ], 422);
            }

            $menu = StmMenuv2::create([
                'id' => $newId,
                'id_parentmenu' => $request->id_parentmenu,
                'level' => $request->level,
                'menu' => $request->menu,
                'linka' => $request->linka,
                'icon' => $request->icon,
                'urutan' => $request->urutan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil dibuat',
                'data' => $menu
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StmMenuv2 $menu)
    {
        $menu->load('parent');
        return response()->json([
            'success' => true,
            'data' => $menu
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StmMenuv2 $menu)
    {
        $validator = Validator::make($request->all(), [
            'menu' => 'required|string|max:255',
            'level' => 'required|integer|in:1,2',
            'id_parentmenu' => 'nullable|integer|exists:stm_menuv2,id',
            'linka' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'urutan' => 'required|integer|min:1'
        ], [
            'menu.required' => 'Nama menu harus diisi',
            'level.required' => 'Level menu harus dipilih',
            'level.in' => 'Level menu harus 1 (Menu Utama) atau 2 (Sub Menu)',
            'id_parentmenu.exists' => 'Parent menu tidak valid',
            'urutan.required' => 'Urutan menu harus diisi',
            'urutan.integer' => 'Urutan menu harus berupa angka',
            'urutan.min' => 'Urutan menu minimal 1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Validate business rules
            if ($request->level == 1 && $request->id_parentmenu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu Utama tidak boleh memiliki parent menu'
                ], 422);
            }

            if ($request->level == 2 && !$request->id_parentmenu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sub Menu harus memiliki parent menu'
                ], 422);
            }

            // Validate link requirement
            if ($request->level == 1 && !$request->id_parentmenu && !$request->linka) {
                // Check if this main menu has children
                $hasChildren = StmMenuv2::where('id_parentmenu', $menu->id)->exists();
                if (!$hasChildren && !$request->linka) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Menu Utama tanpa Sub Menu harus memiliki link'
                    ], 422);
                }
            }

            if ($request->level == 2 && !$request->linka) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sub Menu harus memiliki link'
                ], 422);
            }

            // Validate icon requirement
            if ($request->level == 1 && !$request->icon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu Utama harus memiliki icon'
                ], 422);
            }

            $menu->update([
                'id_parentmenu' => $request->id_parentmenu,
                'level' => $request->level,
                'menu' => $request->menu,
                'linka' => $request->linka,
                'icon' => $request->icon,
                'urutan' => $request->urutan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil diperbarui',
                'data' => $menu
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StmMenuv2 $menu)
    {
        try {
            // Check if menu has children
            $hasChildren = StmMenuv2::where('id_parentmenu', $menu->id)->exists();
            if ($hasChildren) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus menu yang memiliki sub menu'
                ], 422);
            }

            $menu->delete();

            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get parent menus for dropdown
     */
    public function getParentMenus()
    {
        $parentMenus = StmMenuv2::where('level', 1)
            ->orderBy('urutan', 'asc')
            ->orderBy('menu', 'asc')
            ->get(['id', 'menu']);

        return response()->json([
            'success' => true,
            'data' => $parentMenus
        ]);
    }
}
