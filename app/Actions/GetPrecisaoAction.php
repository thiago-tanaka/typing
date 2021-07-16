<?php

namespace App\Actions;

class GetPrecisaoAction
{
    public function __invoke($licao): int
    {
        $precisao = trim(explode('/', $licao)[1]);
        return (int)filter_var($precisao, FILTER_SANITIZE_NUMBER_INT);
    }
}
