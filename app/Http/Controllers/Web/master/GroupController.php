<?php

namespace App\Http\Controllers\Web\master;

use Illuminate\Http\Request;
use App\Models\ConfGroup;
use App\Models\StmMenuv2;
use App\Models\ConfRoleMenu;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('groups.index');
    }

    /**
     * Get groups data via AJAX
     */
    public function getGroups(Request $request)
    {
        $query = ConfGroup::withCount('users');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('group', 'like', "%{$search}%");
        }

        // Per page parameter
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $groups = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'groups' => $groups->items(),
            'pagination' => [
                'current_page' => $groups->currentPage(),
                'last_page' => $groups->lastPage(),
                'per_page' => $groups->perPage(),
                'total' => $groups->total(),
                'from' => $groups->firstItem(),
                'to' => $groups->lastItem(),
                'has_more_pages' => $groups->hasMorePages(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group' => 'required|string|max:255|unique:conf_group,group',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get the highest ID and add 1
            $maxId = ConfGroup::max('conf_group_id') ?? 0;
            $newId = $maxId + 1;

            $group = ConfGroup::create([
                'conf_group_id' => $newId,
                'group' => $request->group,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Group berhasil dibuat',
                'group' => $group
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
    public function show(ConfGroup $group)
    {
        return response()->json([
            'success' => true,
            'group' => $group
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ConfGroup $group)
    {
        $validator = Validator::make($request->all(), [
            'group' => 'required|string|max:255|unique:conf_group,group,' . $group->conf_group_id . ',conf_group_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $group->update([
                'group' => $request->group,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Group berhasil diperbarui',
                'group' => $group
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
    public function destroy(ConfGroup $group)
    {
        try {
            // Check if group has users
            if ($group->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus group yang memiliki user'
                ], 422);
            }

            $group->delete();

            return response()->json([
                'success' => true,
                'message' => 'Group berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all menus for permission management
     */
    public function getMenus()
    {
        $menus = StmMenuv2::orderBy('urutan')->get();

        return response()->json([
            'success' => true,
            'menus' => $menus
        ]);
    }

    /**
     * Get group permissions
     */
    public function getGroupPermissions(ConfGroup $group)
    {
        $permissions = ConfRoleMenu::where('conf_group_id', $group->conf_group_id)
            ->pluck('stm_menu_id')
            ->toArray();

        return response()->json([
            'success' => true,
            'permissions' => $permissions
        ]);
    }

    /**
     * Update group permissions
     */
    public function updatePermissions(Request $request, ConfGroup $group)
    {
        // Get permissions from request (array from FormData)
        $permissions = $request->input('permissions', []);

        $validator = Validator::make([
            'permissions' => $permissions
        ], [
            'permissions' => 'required|array',
            'permissions.*' => 'integer|exists:stm_menuv2,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Delete existing permissions
            ConfRoleMenu::where('conf_group_id', $group->conf_group_id)->delete();

            // Insert new permissions with auto-increment ID
            if (!empty($permissions)) {
                $maxId = ConfRoleMenu::max('id') ?? 0;
                $permissionData = [];

                foreach ($permissions as $menuId) {
                    $maxId++;
                    $permissionData[] = [
                        'id' => $maxId,
                        'conf_group_id' => $group->conf_group_id,
                        'stm_menu_id' => $menuId
                    ];
                }

                ConfRoleMenu::insert($permissionData);
            }

            return response()->json([
                'success' => true,
                'message' => 'Permission berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
