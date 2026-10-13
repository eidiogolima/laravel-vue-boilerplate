<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ErrorLogContext;
use App\Models\ErrorLog;
use Illuminate\Database\Eloquent\Factories\Factory;
use RuntimeException;

/**
 * @extends Factory<ErrorLog>
 */
class ErrorLogFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $file = 'app/Actions/'.fake()->word().'.php';
        $line = fake()->numberBetween(1, 200);

        return [
            'hash' => sha1(RuntimeException::class.'|'.$file.'|'.$line),
            'status_code' => 500,
            'exception' => RuntimeException::class,
            'message' => fake()->sentence(),
            'file' => $file,
            'line' => $line,
            'code_snippet' => [],
            'trace' => [],
            'context' => ErrorLogContext::Http,
            'method' => 'GET',
            'url' => fake()->url(),
            'route' => 'dashboard',
            'ip' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'user_id' => null,
            'payload' => [],
            'headers' => [],
            'environment' => 'testing',
            'resolved_at' => null,
        ];
    }

    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'resolved_at' => now(),
        ]);
    }

    public function withStatusCode(int $statusCode): static
    {
        return $this->state(fn (array $attributes) => [
            'status_code' => $statusCode,
        ]);
    }

    public function sameErrorAs(ErrorLog $errorLog): static
    {
        return $this->state(fn (array $attributes) => [
            'hash' => $errorLog->hash,
            'exception' => $errorLog->exception,
            'file' => $errorLog->file,
            'line' => $errorLog->line,
        ]);
    }
}
