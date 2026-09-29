<?php

namespace Tests\Feature;

use App\Models\QuoteRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteRetentionTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_inquiries_older_than_twelve_months_are_pruned(): void
    {
        $expired = QuoteRequest::create([
            'name' => 'Old request', 'email' => 'old@example.org',
            'project_brief' => 'An old project request.', 'status' => 'new',
        ]);
        $expired->timestamps = false;
        $expired->created_at = now()->subMonths(13);
        $expired->save();

        $recent = QuoteRequest::create([
            'name' => 'Recent request', 'email' => 'recent@example.org',
            'project_brief' => 'A recent project request.', 'status' => 'new',
        ]);

        $this->artisan('keenguild:prune-quote-requests')->assertSuccessful();

        $this->assertDatabaseMissing('quote_requests', ['id' => $expired->id]);
        $this->assertDatabaseHas('quote_requests', ['id' => $recent->id]);
    }
}
