<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserFirstNameTest extends TestCase
{
    /**
     * Test extraction of first name from standard full name.
     */
    public function test_extracts_first_name_from_standard_name(): void
    {
        $user = new User(['first_name' => 'John', 'last_name' => 'Doe']);
        $this->assertEquals('John', $user->first_name_display);
    }

    /**
     * Test extraction from single name.
     */
    public function test_extracts_from_single_name(): void
    {
        $user = new User(['first_name' => 'John', 'last_name' => '']);
        $this->assertEquals('John', $user->first_name_display);
    }

    /**
     * Test extraction from name with multiple words in first_name.
     */
    public function test_extracts_first_word_from_multiple_word_first_name(): void
    {
        $user = new User(['first_name' => 'Muhammad Rizky', 'last_name' => 'Pratama']);
        $this->assertEquals('Muhammad', $user->first_name_display);
    }

    /**
     * Test extraction with title (Gelar).
     */
    public function test_skips_common_titles(): void
    {
        $user = new User(['first_name' => 'Dr. Strange', 'last_name' => '']);
        $this->assertEquals('Strange', $user->first_name_display);

        $user2 = new User(['first_name' => 'Ir. Soekarno', 'last_name' => '']);
        $this->assertEquals('Soekarno', $user2->first_name_display);
        
        $user3 = new User(['first_name' => 'Prof. Dr. Habibie', 'last_name' => '']);
        $this->assertEquals('Habibie', $user3->first_name_display);
    }

    /**
     * Test handling of extra spaces.
     */
    public function test_handles_extra_spaces(): void
    {
        $user = new User(['first_name' => '  Jane   ', 'last_name' => 'Doe']);
        $this->assertEquals('Jane', $user->first_name_display);
    }

    /**
     * Test fallback when first_name is empty but last_name exists (edge case).
     */
    public function test_fallback_to_last_name_if_first_name_empty(): void
    {
        // If first_name is somehow empty but last name isn't
        $user = new User(['first_name' => '', 'last_name' => 'Doe']);
        $this->assertEquals('Doe', $user->first_name_display);
    }
    
    /**
     * Test null/empty handling.
     */
    public function test_handles_empty_name(): void
    {
        $user = new User(['first_name' => null, 'last_name' => null]);
        // Expect 'User' or empty string depending on implementation. 
        // Let's assume we return 'User' as fallback or empty string.
        // Prompt says "handle edge cases like empty or null".
        // In AuthenticatedLayout it uses `user?.name || 'User'`.
        // So the attribute can return null or empty string, and UI handles fallback.
        // Or the attribute returns 'User'.
        // Let's make it return 'User' if totally empty.
        $this->assertEquals('User', $user->first_name_display);
    }
}
