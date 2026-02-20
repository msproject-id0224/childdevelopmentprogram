<?php

use Illuminate\Support\Facades\Storage;

$path = 'test_folder\test_file.txt';
Storage::disk('public')->put($path, 'content');

try {
    $content = Storage::disk('public')->get($path);
    echo "Read successful with backslashes: $content\n";
} catch (\Exception $e) {
    echo "Read failed with backslashes: " . $e->getMessage() . "\n";
}

$path2 = 'test_folder/test_file.txt';
try {
    $content = Storage::disk('public')->get($path2);
    echo "Read successful with forward slashes: $content\n";
} catch (\Exception $e) {
    echo "Read failed with forward slashes: " . $e->getMessage() . "\n";
}

Storage::disk('public')->delete($path);
