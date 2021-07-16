<?php

namespace App\Actions;

class LicaoIsValid
{
    public function __invoke($licao): int
    {
        return strpos($licao, '/') !== false;
    }
}
