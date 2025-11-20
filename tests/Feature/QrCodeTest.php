<?php

namespace Tests\Feature;

use App\Models\KsoItem;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrCodeTest extends TestCase
{
    use RefreshDatabase;

    protected $customer;
    protected $ksoItem;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test customer
        $this->customer = Customer::factory()->create([
            'name' => 'Test Hospital',
            'email' => 'test@hospital.com'
        ]);

        // Create test KSO Item
        $this->ksoItem = KsoItem::factory()->create([
            'customer_id' => $this->customer->id,
            'nama_alat' => 'Test Equipment',
            'nilai_alat_utama' => 100000000,
            'status' => 'active'
        ]);
    }

    /**
     * Test QR password form can be accessed
     */
    public function test_qr_password_form_can_be_accessed()
    {
        $response = $this->get(route('qr.password', $this->ksoItem->id));

        $response->assertStatus(200);
        $response->assertViewIs('kso-roi.qr-password');
        $response->assertViewHas('ksoItem', $this->ksoItem);
    }

    /**
     * Test QR password form shows error for non-existent KSO Item
     */
    public function test_qr_password_form_shows_error_for_non_existent_item()
    {
        $response = $this->get(route('qr.password', 999));

        $response->assertStatus(200);
        $response->assertViewHas('error', 'KSO Item tidak ditemukan');
    }

    /**
     * Test password verification with correct password
     */
    public function test_password_verification_with_correct_password()
    {
        $response = $this->post(
            route('qr.verify', $this->ksoItem->id),
            ['password' => config('app.qr_password')]
        );

        $response->assertRedirect(route('qr.detail', $this->ksoItem->id));
        $this->assertSessionHas('qr_verified_' . $this->ksoItem->id);
    }

    /**
     * Test password verification with wrong password
     */
    public function test_password_verification_with_wrong_password()
    {
        $response = $this->post(
            route('qr.verify', $this->ksoItem->id),
            ['password' => 'WrongPassword']
        );

        $response->assertRedirect(route('qr.password', $this->ksoItem->id));
        $response->assertSessionHasErrors();
        $this->assertSessionMissing('qr_verified_' . $this->ksoItem->id);
    }

    /**
     * Test password verification requires password field
     */
    public function test_password_verification_requires_password_field()
    {
        $response = $this->post(
            route('qr.verify', $this->ksoItem->id),
            []
        );

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test QR detail page can be accessed after password verification
     */
    public function test_qr_detail_page_after_password_verification()
    {
        // First verify password
        $this->post(
            route('qr.verify', $this->ksoItem->id),
            ['password' => config('app.qr_password')]
        );

        // Then access detail page
        $response = $this->get(route('qr.detail', $this->ksoItem->id));

        $response->assertStatus(200);
        $response->assertViewIs('kso-roi.qr-detail');
        $response->assertViewHas('ksoItem', $this->ksoItem);
    }

    /**
     * Test QR detail page redirects without password verification
     */
    public function test_qr_detail_page_redirects_without_verification()
    {
        $response = $this->get(route('qr.detail', $this->ksoItem->id));

        $response->assertRedirect(route('qr.password', $this->ksoItem->id));
        $response->assertSessionHas('error', 'Silakan masukkan password terlebih dahulu');
    }

    /**
     * Test QR detail page shows error for non-existent KSO Item
     */
    public function test_qr_detail_page_shows_error_for_non_existent_item()
    {
        // Set verified session
        $this->session(['qr_verified_999' => true]);

        $response = $this->get(route('qr.detail', 999));

        $response->assertStatus(200);
        $response->assertViewHas('error', 'KSO Item tidak ditemukan');
    }

    /**
     * Test QR detail page displays all KSO Item information
     */
    public function test_qr_detail_page_displays_all_information()
    {
        // Verify password
        $this->post(
            route('qr.verify', $this->ksoItem->id),
            ['password' => config('app.qr_password')]
        );

        $response = $this->get(route('qr.detail', $this->ksoItem->id));

        $response->assertSee($this->ksoItem->nama_alat);
        $response->assertSee($this->customer->name);
        $response->assertSee('Peralatan Utama');
        $response->assertSee('Tanggal & Periode');
        $response->assertSee('Lokasi & Penanggung Jawab');
    }

    /**
     * Test QR password form displays KSO Item information
     */
    public function test_qr_password_form_displays_kso_item_info()
    {
        $response = $this->get(route('qr.password', $this->ksoItem->id));

        $response->assertSee($this->ksoItem->nama_alat);
        $response->assertSee($this->customer->name);
    }

    /**
     * Test multiple password attempts
     */
    public function test_multiple_password_attempts()
    {
        // First attempt - wrong password
        $response1 = $this->post(
            route('qr.verify', $this->ksoItem->id),
            ['password' => 'WrongPassword1']
        );
        $response1->assertSessionHasErrors();

        // Second attempt - wrong password
        $response2 = $this->post(
            route('qr.verify', $this->ksoItem->id),
            ['password' => 'WrongPassword2']
        );
        $response2->assertSessionHasErrors();

        // Third attempt - correct password
        $response3 = $this->post(
            route('qr.verify', $this->ksoItem->id),
            ['password' => config('app.qr_password')]
        );
        $response3->assertRedirect(route('qr.detail', $this->ksoItem->id));
        $this->assertSessionHas('qr_verified_' . $this->ksoItem->id);
    }

    /**
     * Test session verification is per KSO Item
     */
    public function test_session_verification_is_per_kso_item()
    {
        // Create another KSO Item
        $anotherItem = KsoItem::factory()->create([
            'customer_id' => $this->customer->id,
            'nama_alat' => 'Another Equipment'
        ]);

        // Verify password for first item
        $this->post(
            route('qr.verify', $this->ksoItem->id),
            ['password' => config('app.qr_password')]
        );

        // Check first item is verified
        $this->assertSessionHas('qr_verified_' . $this->ksoItem->id);

        // Check second item is not verified
        $this->assertSessionMissing('qr_verified_' . $anotherItem->id);

        // Try to access second item detail - should redirect
        $response = $this->get(route('qr.detail', $anotherItem->id));
        $response->assertRedirect(route('qr.password', $anotherItem->id));
    }

    /**
     * Test QR detail page with support items
     */
    public function test_qr_detail_page_displays_support_items()
    {
        // Create support items
        $this->ksoItem->supportItems()->create([
            'nama_item' => 'Monitor',
            'jumlah' => 2,
            'nilai_item' => 5000000
        ]);

        // Verify password
        $this->post(
            route('qr.verify', $this->ksoItem->id),
            ['password' => config('app.qr_password')]
        );

        $response = $this->get(route('qr.detail', $this->ksoItem->id));

        $response->assertSee('Peralatan Pendukung');
        $response->assertSee('Monitor');
        $response->assertSee('Qty: 2');
    }

    /**
     * Test password field validation
     */
    public function test_password_field_validation()
    {
        $response = $this->post(
            route('qr.verify', $this->ksoItem->id),
            ['password' => '']
        );

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test case sensitivity of password
     */
    public function test_password_case_sensitivity()
    {
        $correctPassword = config('app.qr_password');
        $wrongCasePassword = strtolower($correctPassword);

        if ($correctPassword !== $wrongCasePassword) {
            $response = $this->post(
                route('qr.verify', $this->ksoItem->id),
                ['password' => $wrongCasePassword]
            );

            $response->assertSessionHasErrors();
        }
    }
}
