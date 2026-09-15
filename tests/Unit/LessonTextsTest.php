<?php

namespace Tests\Unit;

use Tests\TestCase;

class LessonTextsTest extends TestCase
{
    public function test_default_lessons_match_the_navigation()
    {
        $this->assertSame([1, 2, 3], array_keys(config('licoes')));

        foreach (config('licoes') as $unidade => $licoes) {
            $this->assertSame([1, 2, 3, 4, 5], array_keys($licoes), "unit $unidade");

            foreach ($licoes as $licao => $textos) {
                $this->assertSame([1, 2, 3, 4], array_keys($textos), "$unidade/$licao");
            }
        }
    }

    public function test_default_lessons_only_use_keys_in_the_same_place_on_any_layout()
    {
        foreach (config('licoes') as $unidade => $licoes) {
            foreach ($licoes as $licao => $textos) {
                foreach ($textos as $texto) {
                    $this->assertMatchesRegularExpression('/^[a-z ,.]+$/', $texto, "$unidade/$licao");
                    $this->assertLessThanOrEqual(255, strlen($texto), "$unidade/$licao");
                }
            }
        }
    }
}
