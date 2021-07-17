<?php

namespace App\Http\Controllers;

use App\Models\Lesson;

class LessonController extends Controller
{
    public function update(Lesson $lesson)
    {
        $lesson->update([
            'text1' => request('text1'),
            'text2' => request('text2'),
            'text3' => request('text3'),
            'text4' => request('text4'),
        ]);
        return [
            'lesson' => $lesson,
            'message' => 'Lesson saved!'
        ];
    }
}
