<?php

namespace App\Actions;

class PontuacaoNovaEMaiorAction
{
    public function __invoke($current, string $new): bool
    {
        if (!$current) {
            return true;
        }

        $current_precisao = (new GetPrecisaoAction)($current);
        $current_valocidade = (new GetVelocidadeAction)($current);

        $new_precisao = (new GetPrecisaoAction)($new);
        $new_velocidade = (new GetVelocidadeAction)($new);

        return $new_precisao >= $current_precisao &&
            $new_velocidade >= $current_valocidade;
    }
}
