<?php

namespace Integration\Web;

use PHPUnit\Framework\TestCase;

class RestTest extends TestCase
{
    public function testApiDeveRetornarUmJsonDeLeiloes()
    {
        $resposta = exec( 'php rest.php');

        $this->assertJson($resposta);

        $this->assertJsonStringEqualsJsonString(
            '[{"descricao":"Leil\u00e3o 1","estaFinalizado":false},{"descricao":"Leil\u00e3o 2",'
            . '"estaFinalizado":false},{"descricao":"Leil\u00e3o 3","estaFinalizado":false},{"descricao":"Leil\u00e3o'
            . ' 4","estaFinalizado":false}]',
            $resposta
        );
    }
}