<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$role = App\Models\Role::where('name','Encoder')->first();
if (!$role) {
    echo "no encoder role\n";
    exit;
}
echo "role id={$role->id}\n";
$perms = $role->permissions->pluck('name')->toArray();
print_r($perms);
$user = App\Models\User::where('role_id',$role->id)->first();
if (!$user) {
    echo "no encoder user\n";
    exit;
}
echo "encoder user: {$user->name}, id={$user->id}, role={$user->role->name}\n";

echo "has add_households: ".($user->hasPermission('add_households') ? 'yes' : 'no')."\n";
echo "has update_households: ".($user->hasPermission('update_households') ? 'yes' : 'no')."\n";
