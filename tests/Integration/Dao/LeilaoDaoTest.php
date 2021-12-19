<?php

namespace Integration\Dao;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Model\Leilao;
use PHPUnit\Framework\TestCase;

class LeilaoDaoTest extends TestCase
{
    private static \PDO $pdo;
    private LeilaoDao $leilaoDao;

    public static function setUpBeforeClass(): void
    {
        self::$pdo = new \PDO('sqlite::memory:');
        self::$pdo->exec('create table leiloes (
            id INTEGER primary key,
            descricao  TEXT,
            finalizado BOOL,
            dataInicio TEXT
        );');
    }

    protected function setUp(): void
    {
        self::$pdo->beginTransaction();
        $this->leilaoDao = new LeilaoDao(self::$pdo);
    }

    /**
     * @dataProvider leiloes
     */
    public function testBuscaLeiloesNaoFinalizados(array $leiloes)
    {
        foreach ($leiloes as $leilao) {
            $this->leilaoDao->salva($leilao);
        }

        $leiloesRecuperados = $this->leilaoDao->recuperarNaoFinalizados();

        $this->assertCount(1, $leiloesRecuperados);
        $this->assertContainsOnlyInstancesOf(Leilao::class, $leiloesRecuperados);

        foreach ($leiloesRecuperados as $leilao) {
            $this->assertSame('Camaro 2018', $leilao->recuperarDescricao());
        }
    }

    /**
     * @dataProvider leiloes
     */
    public function testBuscaLeiloesFinalizados(array $leiloes)
    {
        foreach ($leiloes as $leilao) {
            $this->leilaoDao->salva($leilao);
        }

        $leiloesRecuperados = $this->leilaoDao->recuperarFinalizados();

        $this->assertCount(1, $leiloesRecuperados);
        $this->assertContainsOnlyInstancesOf(Leilao::class, $leiloesRecuperados);

        foreach ($leiloesRecuperados as $leilao) {
            $this->assertSame('Dodge 2019', $leilao->recuperarDescricao());
        }
    }

    public function testAoAtualizarLeiloesStatusDeveSerAlterado()
    {
        $leilao = new Leilao('Ferrari 2020');
        $leilao = $this->leilaoDao->salva($leilao);

        $leilao->finaliza();
        $this->leilaoDao->atualiza($leilao);

        $leiloes = $this->leilaoDao->recuperarFinalizados();

        $this->assertCount(1, $leiloes);

        foreach ($leiloes as $leilao) {
            $this->assertSame('Ferrari 2020', $leilao->recuperarDescricao());
            $this->assertTrue($leilao->estaFinalizado());
        }
    }

    public function leiloes()
    {
        $naoFinalizado = new Leilao('Camaro 2018');
        $leiloes[] = $naoFinalizado;

        $finalizado = new Leilao('Dodge 2019');
        $finalizado->finaliza();
        $leiloes[] = $finalizado;

        return [
            [$leiloes]
        ];
    }

    protected function tearDown(): void
    {
         self::$pdo->rollBack();
    }
}