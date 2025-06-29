<?php

namespace App\Trait;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function Symfony\Component\Translation\t;

trait AdminPermissionTrait
{
    protected function getAccessPermission($request):bool
    {

        $user = auth('admin')->user();

        if (!$user) {
            return false;
        }
        $role = $user?->role;
        if (empty($role) || !$role->status  ) {
            return false;
        }

        $permissions = json_decode($role->access_permissions, true);
        $slug = $this->extractSlug($request);
        $action = $this->determineAction($request);
        $permissionExists = DB::table('admin_permissions')
            ->where('module', $slug)
            ->whereRaw('JSON_CONTAINS(`actions`, ?)', ['"' . $action . '"'])
            ->exists();
        if (!$permissionExists) {
            return true;
        }
        if (!$slug || !$action) {
            return false;
        }
        if (!isset($permissions[$slug]) || !in_array($action, $permissions[$slug])) {
            return false;
        }
        return true;
    }
    protected function extractSlug(Request $request): ?string
    {
        $segments = $request->segments();

        $adminPrefix = 'admin';

        if ($segments[0] === $adminPrefix) {
            return $segments[1] ?? null;
        }

        return null;
    }

    protected function determineAction(Request $request): ?string
    {
        $method = $request->method();
        $path = $request->path();
        $payload =$request->all();
        if ($request->hasHeader('X-Livewire') && isset($payload['components'][0]['calls'][0]['method'])) {
            $method = $payload['components'][0]['calls'][0]['method'];
            if ($method === 'updateTableColumnState') {
              return 'update_status';
            }
            if ($method === 'mountFormComponentAction') {
              return 'update';
            }
        }

        if (str_ends_with($path, 'create')) return 'create';
        if (str_ends_with($path, 'edit')) return 'edit';
        if ($method === 'DELETE') return 'delete';

        return 'view';
    }
}
