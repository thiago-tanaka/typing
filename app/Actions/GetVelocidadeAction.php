<?php

namespace App\Actions;

class GetVelocidadeAction
{
    public function __invoke($licao): int
    {
        return (int)trim(explode('/', $licao)[0]);
    }
}
