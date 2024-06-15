<?php

require_once dirname(__FILE__) . '/../nivel_detritos.php';

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    public function testNivelDetritosVazios()
    {
        $resultado = obterNivelDetritos(76);
        $esperado = 'Vazio';

        $this->assertEquals($esperado, $resultado);
    }

    public function testNivelDetritosAtencao()
    {
        $resultado = obterNivelDetritos(41);
        $esperado = 'Precisa de Atenção';

        $this->assertEquals($esperado, $resultado);
    }

    public function testNivelDetritosEntupido()
    {
        $resultado = obterNivelDetritos(39);
        $esperado = 'Entupido';

        $this->assertEquals($esperado, $resultado);
    }


}
