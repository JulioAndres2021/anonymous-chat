<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackupStorageTest extends TestCase
{
    public function test_disk_de_backups_esta_configurado(): void
    {
        Storage::fake('backups');

        Storage::disk('backups')->put(
            'test.txt',
            'ok'
        );

        Storage::disk('backups')
            ->assertExists('test.txt');
    }
}