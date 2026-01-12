<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Webkul\User\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::find(1);

if ($user) {
    // Explicitly hashing 'admin123'
    $user->password = Hash::make('admin123');
    $user->save();
    echo "Password for user ID 1 ({$user->email}) has been reset to 'admin123'.\n";
} else {
    echo "User ID 1 not found.\n";
}
