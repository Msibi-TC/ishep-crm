<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomePageTest extends TestCase
{
    /**
     * Test the home page loads successfully.
     */
    public function test_home_page_loads(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('ISHEP');
        $response->assertSee('Member engagement platform');
    }

    /**
     * Test the membership portal page loads successfully.
     */
    public function test_membership_page_loads(): void
    {
        $response = $this->get('/membership');

        $response->assertOk();
        $response->assertSee('Membership');
    }

    /**
     * Test the careers portal page loads successfully.
     */
    public function test_careers_page_loads(): void
    {
        $response = $this->get('/careers');

        $response->assertOk();
        $response->assertSee('Career');
    }

    /**
     * Test the bursaries portal page loads successfully.
     */
    public function test_bursaries_page_loads(): void
    {
        $response = $this->get('/bursaries');

        $response->assertOk();
        $response->assertSee('Bursaries');
    }

    /**
     * Test the membership verification page loads successfully.
     */
    public function test_verify_membership_page_loads(): void
    {
        $response = $this->get('/verify-membership');

        $response->assertOk();
        $response->assertSee('Verification');
    }

    /**
     * Test the login page loads successfully.
     */
    public function test_login_page_loads(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('Login');
    }

    /**
     * Test the registration page loads successfully.
     */
    public function test_register_page_loads(): void
    {
        $response = $this->get('/register');

        $response->assertOk();
        $response->assertSee('Create account');
    }
}
