<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Test sitemap.xml returns valid XML.
     */
    public function test_sitemap_returns_successful_xml_response(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml');
        $this->assertStringContainsString('<urlset', $response->getContent());
        $this->assertStringContainsString('<loc>', $response->getContent());
    }

    /**
     * Test login page has noindex meta tag.
     */
    public function test_login_page_has_noindex_meta(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    /**
     * Test public dashboard has dynamic SEO tags & schema.org JSON-LD.
     */
    public function test_public_dashboard_has_correct_seo_meta(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Page Title
        $response->assertSee('RT.011 Karanggintung - Portal Informasi Warga dan Transparansi Keuangan');
        // Meta description
        $response->assertSee('<meta name="description" content="Portal informasi warga RT.011 Karanggintung untuk melihat ringkasan keuangan, status pembayaran iuran, dan layanan administrasi RT secara transparan.">', false);
        // Schema JSON-LD
        $response->assertSee('application/ld+json', false);
        $response->assertSee('RT.011 Karanggintung', false);
        // H1 Heading
        $response->assertSee('<h1 class="display-5 fw-bold mb-2 hero-title">Portal Informasi Warga RT.011 Karanggintung</h1>', false);
        // H2 Semantic Headings
        $response->assertSee('Tentang RT.011 Karanggintung', false);
        $response->assertSee('Layanan Warga & Administrasi', false); // Literal &
        $response->assertSee('Transparansi Keuangan RT', false);
    }
}
