<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Config;

class PageTitleTest extends TestCase
{
    /**
     * Test that the application name is correct.
     */
    public function test_app_name_is_correct()
    {
        $this->assertEquals('Child Development Program', config('app.name'));
    }

    /**
     * Test that the root page has the correct title.
     */
    public function test_root_page_has_correct_title()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Check for the Inertia title tag which uses config('app.name')
        $response->assertSee('<title inertia>Child Development Program</title>', false);
    }
}
