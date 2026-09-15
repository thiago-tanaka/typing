<?php

namespace App\Models;

class Digitacao
{
    public const VELOCIDADE_OTIMA = 250;
    public const VELOCIDADE_BOA = 160;
    public const VELOCIDADE_MEDIA = 75;

    public const PRECISAO_OTIMA = 98;
    public const PRECISAO_BOA = 97;
    public const PRECISAO_MEDIA = 96;

    // Highest speed accepted when saving a score: faster than any human typist.
    public const VELOCIDADE_MAXIMA = 2000;

    public const NIVEL_OTIMO = 'excellent';
    public const NIVEL_BOM = 'good';
    public const NIVEL_MEDIO = 'average';
    public const NIVEL_RUIM = 'practice';

    /**
     * Score levels from best to worst, with the minimum speed (characters per
     * minute) and accuracy (%) needed to reach each one.
     */
    public static function niveis(): array
    {
        return [
            ['nivel' => self::NIVEL_OTIMO, 'velocidade' => self::VELOCIDADE_OTIMA, 'precisao' => self::PRECISAO_OTIMA],
            ['nivel' => self::NIVEL_BOM, 'velocidade' => self::VELOCIDADE_BOA, 'precisao' => self::PRECISAO_BOA],
            ['nivel' => self::NIVEL_MEDIO, 'velocidade' => self::VELOCIDADE_MEDIA, 'precisao' => self::PRECISAO_MEDIA],
        ];
    }

    public static function nivel($velocidade, $precisao): string
    {
        foreach (self::niveis() as $nivel) {
            if ($velocidade >= $nivel['velocidade'] && $precisao >= $nivel['precisao']) {
                return $nivel['nivel'];
            }
        }

        return self::NIVEL_RUIM;
    }

    /**
     * Position of a level, from the best (0) to the worst.
     */
    public static function posicaoNivel(string $nivel): int
    {
        $ordem = [...array_column(self::niveis(), 'nivel'), self::NIVEL_RUIM];

        return array_search($nivel, $ordem, true);
    }

    /**
     * Speed adjusted by accuracy, in characters per minute.
     */
    public static function velocidadeLiquida($velocidade, $precisao): float
    {
        return $velocidade * $precisao / 100;
    }

    /**
     * Compares two results: the higher level wins and, within the same level,
     * the higher net speed wins. Returns a positive number when A is better,
     * a negative number when B is better and 0 when they are equivalent.
     */
    public static function compararResultados($velocidadeA, $precisaoA, $velocidadeB, $precisaoB): int
    {
        $nivel = self::posicaoNivel(self::nivel($velocidadeB, $precisaoB))
            <=> self::posicaoNivel(self::nivel($velocidadeA, $precisaoA));

        if ($nivel !== 0) {
            return $nivel;
        }

        return self::velocidadeLiquida($velocidadeA, $precisaoA) <=> self::velocidadeLiquida($velocidadeB, $precisaoB);
    }
}
