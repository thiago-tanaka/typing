<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitsTest extends TestCase
{
    use RefreshDatabase;

    public function test_units_are_listed_with_their_lessons_for_anyone()
    {
        $unit = Unit::factory()->create(['name' => '1']);
        Lesson::factory()->for($unit)->create(['name' => '1', 'text1' => 'primeira']);
        Lesson::factory()->for($unit)->create(['name' => '2']);

        $response = $this->getJson('/units');

        $response->assertOk();
        $response->assertJsonCount(1, 'units');
        $response->assertJsonPath('units.0.name', '1');
        $response->assertJsonCount(2, 'units.0.lessons');
        $response->assertJsonPath('units.0.lessons.0.text1', 'primeira');
    }
}
