<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SqlInjectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    // حمولة 1: تعطيل شرط is_public وإظهار المنتجات السرية
    private const BYPASS = "' OR 1=1 -- ";

    // حمولة 2: UNION لسرقة بريد المستخدمين وتجزئة كلمات مرورهم (4 أعمدة كما في الاستعلام الأصلي)
    private const UNION = "' UNION SELECT id, email, password, 0 FROM users -- ";

    // ---------- قبل الإصلاح: الثغرة موجودة ----------

    public function test_vulnerable_search_works_normally(): void
    {
        $this->get('/vulnerable/search?q=Laptop')
            ->assertOk()
            ->assertSee('Laptop Pro 15')
            ->assertDontSee('Prototype X');
    }

    public function test_vulnerable_search_leaks_hidden_products(): void
    {
        $this->get('/vulnerable/search?q=' . urlencode(self::BYPASS))
            ->assertOk()
            ->assertSee('Prototype X (confidential)')
            ->assertSee('STAFF-70-OFF');
    }

    public function test_vulnerable_search_leaks_users_table(): void
    {
        $this->get('/vulnerable/search?q=' . urlencode(self::UNION))
            ->assertOk()
            ->assertSee('admin@lab.test')
            ->assertSee('student@lab.test');
    }

    // ---------- بعد الإصلاح: الثغرة مسدودة ----------

    public function test_secure_search_works_normally(): void
    {
        $this->get('/secure/search?q=Laptop')
            ->assertOk()
            ->assertSee('Laptop Pro 15')
            ->assertDontSee('Prototype X');
    }

    public function test_secure_search_does_not_leak_hidden_products(): void
    {
        $this->get('/secure/search?q=' . urlencode(self::BYPASS))
            ->assertOk()
            ->assertDontSee('Prototype X (confidential)')
            ->assertDontSee('STAFF-70-OFF');
    }

    public function test_secure_search_does_not_leak_users_table(): void
    {
        $this->get('/secure/search?q=' . urlencode(self::UNION))
            ->assertOk()
            ->assertDontSee('admin@lab.test')
            ->assertDontSee('student@lab.test');
    }

    public function test_secure_search_rejects_overlong_input(): void
    {
        $this->get('/secure/search?q=' . str_repeat('a', 101))
            ->assertSessionHasErrors('q');
    }
}
