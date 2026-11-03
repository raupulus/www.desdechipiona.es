<?php

namespace Tests\Unit\Rules;

use App\Rules\Turnstile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TurnstileRuleTest extends TestCase
{
    public function test_turnstile_skips_validation_when_keys_are_empty(): void
    {
        config([
            'services.turnstile.site_key' => '',
            'services.turnstile.secret_key' => '',
        ]);

        $failed = false;
        $fail = function () use (&$failed) {
            $failed = true;
        };

        $rule = new Turnstile;
        $rule->validate('cf-turnstile-response', null, $fail);

        $this->assertFalse($failed);
    }

    public function test_turnstile_fails_when_token_is_missing_with_keys_configured(): void
    {
        config([
            'services.turnstile.site_key' => 'dummy-site-key',
            'services.turnstile.secret_key' => 'dummy-secret-key',
        ]);

        $failed = false;
        $fail = function () use (&$failed) {
            $failed = true;
        };

        $rule = new Turnstile;
        $rule->validate('cf-turnstile-response', '', $fail);

        $this->assertTrue($failed);
    }

    public function test_turnstile_passes_when_cloudflare_verifies_successfully(): void
    {
        config([
            'services.turnstile.site_key' => 'dummy-site-key',
            'services.turnstile.secret_key' => 'dummy-secret-key',
        ]);

        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
                'action' => 'comment',
            ], 200),
        ]);

        $failed = false;
        $fail = function () use (&$failed) {
            $failed = true;
        };

        $rule = new Turnstile('comment');
        $rule->validate('cf-turnstile-response', 'valid-turnstile-token', $fail);

        $this->assertFalse($failed);
    }

    public function test_turnstile_fails_when_action_mismatches(): void
    {
        config([
            'services.turnstile.site_key' => 'dummy-site-key',
            'services.turnstile.secret_key' => 'dummy-secret-key',
        ]);

        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
                'action' => 'login',
            ], 200),
        ]);

        $failed = false;
        $fail = function () use (&$failed) {
            $failed = true;
        };

        $rule = new Turnstile('comment');
        $rule->validate('cf-turnstile-response', 'valid-turnstile-token', $fail);

        $this->assertTrue($failed);
    }
}
