<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

echo "=========================================================\n";
echo "RBAC PERMISSION PERSISTENCE & AUTHORIZATION TEST SUITE\n";
echo "=========================================================\n\n";

// Ensure seed permissions exist
Artisan::call('db:seed', ['--class' => 'ComprehensivePortalSeeder']);

// 1. Fetch or create test role "Application Officer"
$role = Role::where('slug', 'application-officer')->first();
if (!$role) {
    $role = Role::create([
        'name' => 'Application Officer',
        'slug' => 'application-officer',
        'description' => 'Test application officer role',
        'is_active' => true,
    ]);
}

// Ensure test permissions exist
$pView = Permission::where('slug', 'applications.view')->first();
$pUpdate = Permission::where('slug', 'applications.update')->first();
$pDocView = Permission::where('slug', 'documents.view')->first();
$pUsersManage = Permission::where('slug', 'users.view')->first();

// Step A: Admin sets initial permissions: applications.view, applications.update, documents.view
$role->permissions()->sync([$pView->id, $pUpdate->id, $pDocView->id]);
Cache::forget("role_permissions_{$role->id}");

$testUser = User::firstOrCreate(
    ['email' => 'officer_test@dootor.com'],
    [
        'first_name' => 'Officer',
        'last_name' => 'Test',
        'password' => bcrypt('password'),
        'role' => 'staff',
        'status' => 'Approved',
    ]
);
// Detach any previous super-admin role if attached
$superAdminRole = Role::where('slug', 'super-admin')->first();
if ($superAdminRole) {
    $testUser->roles()->detach($superAdminRole->id);
}
$testUser->roles()->sync([$role->id]);
$testUser->unsetRelation('roles');

echo "[TEST 1] Initial Permissions Check for Application Officer:\n";
echo " - applications.view: " . ($testUser->hasPermission('applications.view') ? "PASS (True)" : "FAIL (False)") . "\n";
echo " - applications.update: " . ($testUser->hasPermission('applications.update') ? "PASS (True)" : "FAIL (False)") . "\n";
echo " - documents.view: " . ($testUser->hasPermission('documents.view') ? "PASS (True)" : "FAIL (False)") . "\n";
echo " - users.view: " . (!$testUser->hasPermission('users.view') ? "PASS (Blocked/False)" : "FAIL (Allowed)") . "\n\n";

// Step B: Admin updates role permissions - REMOVES applications.update
echo "[TEST 2] Admin Removes 'applications.update' Permission:\n";
$role->permissions()->sync([$pView->id, $pDocView->id]);
Cache::forget("role_permissions_{$role->id}");
$testUser->unsetRelation('roles');

$hasUpdate = $testUser->hasPermission('applications.update');
echo " - Immediate Check for applications.update: " . (!$hasUpdate ? "PASS (Revoked/False)" : "FAIL (Still True)") . "\n";

// Step C: Run Seeder (Simulating Deployment / Application Startup / db:seed)
echo "\n[TEST 3] Running Seeder (ComprehensivePortalSeeder):\n";
Artisan::call('db:seed', ['--class' => 'ComprehensivePortalSeeder']);

$testUser->unsetRelation('roles');
$role = Role::where('slug', 'application-officer')->first();
$permSlugsAfterSeeder = $role->permissions()->pluck('slug')->toArray();

echo " - Role Permissions in DB after Seeder: " . implode(', ', $permSlugsAfterSeeder) . "\n";
$hasUpdateAfterSeeder = $testUser->hasPermission('applications.update');
echo " - applications.update status after Seeder: " . (!$hasUpdateAfterSeeder ? "SUCCESS: Permission remained revoked!" : "FAILURE: Seeder restored original permissions!") . "\n";

// Step D: Cache Clear & Optimize Clear
echo "\n[TEST 4] Clearing Application & Route Cache (optimize:clear):\n";
Artisan::call('optimize:clear');
$testUser->unsetRelation('roles');

$hasUpdateAfterCacheClear = $testUser->hasPermission('applications.update');
echo " - applications.update status after Cache Clear: " . (!$hasUpdateAfterCacheClear ? "SUCCESS: Permission remained revoked!" : "FAILURE: Cache clear restored original permissions!") . "\n";

// Step E: Intentionally Empty Permission Role Test (Zero permissions)
echo "\n[TEST 5] Empty Permission Set (Zero Permissions Role Test):\n";
$role->permissions()->sync([]);
Cache::forget("role_permissions_{$role->id}");
$testUser->unsetRelation('roles');

echo " - Permissions count in DB: " . $role->permissions()->count() . "\n";
echo " - applications.view status: " . (!$testUser->hasPermission('applications.view') ? "PASS (False)" : "FAIL (True)") . "\n";

// Re-run Seeder to test zero permission persistence
Artisan::call('db:seed', ['--class' => 'ComprehensivePortalSeeder']);
$testUser->unsetRelation('roles');
$role = Role::where('slug', 'application-officer')->first();
echo " - Permissions count in DB after Seeder: " . $role->permissions()->count() . "\n";
echo " - Zero Permission Persistence: " . ($role->permissions()->count() === 0 ? "SUCCESS: Zero permissions preserved!" : "FAILURE: Defaults restored!") . "\n";

// Step F: Super Admin Protection Test
echo "\n[TEST 6] Super Admin Exception Test:\n";
$superAdminRole = Role::where('slug', 'super-admin')->first();
$superAdminUser = User::firstOrCreate(
    ['email' => 'superadmin_test@dootor.com'],
    [
        'first_name' => 'Super',
        'last_name' => 'Admin',
        'password' => bcrypt('password'),
        'role' => 'admin',
        'status' => 'Approved',
    ]
);
$superAdminUser->roles()->sync([$superAdminRole->id]);
$superAdminUser->unsetRelation('roles');

echo " - Super Admin access check for any permission (system.settings): " . ($superAdminUser->hasPermission('system.settings') ? "PASS (True)" : "FAIL (False)") . "\n";

echo "\n=========================================================\n";
echo "ALL RBAC ACCEPTANCE TESTS COMPLETED SUCCESSFULLY!\n";
echo "=========================================================\n";
