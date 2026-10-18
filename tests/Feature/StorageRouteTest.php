<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class StorageRouteTest extends TestCase
{
    public function test_can_retrieve_file_from_storage_route(): void
    {
        $testDir = storage_path('app/public/test_folder');
        if (!File::exists($testDir)) {
            File::makeDirectory($testDir, 0755, true);
        }

        $filePath = $testDir . '/hello.txt';
        File::put($filePath, 'Hello Clinic World!');

        $response = $this->get('/storage/test_folder/hello.txt');

        $response->assertStatus(200);
        $this->assertEquals('Hello Clinic World!', $response->streamedContent());

        // Cleanup
        File::deleteDirectory($testDir);
    }

    public function test_non_existent_file_returns_404(): void
    {
        $response = $this->get('/storage/non_existent_file_12345.png');

        $response->assertStatus(404);
    }
}
