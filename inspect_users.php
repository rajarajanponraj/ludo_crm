<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Webkul\User\Models\User;

$users = User::all();

if ($users->isEmpty()) {
    echo "No users found in the database.\n";
} else {
    foreach ($users as $user) {
        echo "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}, Status: {$user->status}, Role ID: {$user->role_id}\n";
    }
}
