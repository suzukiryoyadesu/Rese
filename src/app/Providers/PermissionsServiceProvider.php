<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Blade;
use App\Models\Permission;

class PermissionsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        try {
            Permission::get()->map(function ($perm) {
                Gate::define($perm->name, function ($user) use ($perm) {
                    return $user->hasPermission($perm->name);
                });
            });
        } catch (\Exception $e) {
            Log::error(__FILE__ . " (" . __LINE__ . ")" . PHP_EOL . $e->getMessage());
            return false;
        }

        Blade::directive('role', function ($role) {
            return "if(auth()->check() && auth()->user()->hasRole({$role})) :";
        });

        Blade::directive('endrole', function ($role) {
            return "endif;";
        });
    }
}
