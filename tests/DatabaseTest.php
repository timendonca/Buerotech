<?php

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    public function testDatabaseQueries()
    {
        // Configurar o mock do mysqli
        $mysqliMock = $this->createMock(mysqli::class);
        
        // Mock para a consulta "sensor_data"
        $resultRealtimeMock = $this->createMock(mysqli_result::class);
        $resultRealtimeMock->method('fetch_all')->willReturn([
            ['id' => 1, 'data' => 'test data']
        ]);
        
        // Mock para a consulta "bueiros"
        $resultBueirosMock = $this->createMock(mysqli_result::class);
        $resultBueirosMock->method('fetch_all')->willReturn([
            ['id_bueiro' => 1, 'localizacao' => 'local test']
        ]);
        
        // Mock para a consulta combinada
        $resultCombinadoMock = $this->createMock(mysqli_result::class);
        $resultCombinadoMock->method('fetch_assoc')->willReturnOnConsecutiveCalls(
            ['id_medicao' => 1, 'id_sensor' => 1, 'id_bueiro' => 1, 'distancia' => 45, 'timestamp' => '2023-01-01 00:00:00', 'profundidade' => 100, 'localizacao' => 'local test'],
            null
        );
        $resultCombinadoMock->method('num_rows')->willReturn(1);

        // Configurar o comportamento dos métodos do mysqli
        $mysqliMock->method('query')
            ->will($this->returnValueMap([
                ['SELECT * FROM sensor_data', $resultRealtimeMock],
                ['SELECT * FROM bueiros', $resultBueirosMock],
                ['SELECT sd.id_medicao, sd.id_sensor, sd.id_bueiro, sd.distancia, sd.timestamp, b.profundidade, b.localizacao FROM sensor_data sd INNER JOIN bueiros b ON sd.id_bueiro = b.id_bueiro', $resultCombinadoMock]
            ]));

        // Injetar o mock do mysqli no código que está sendo testado
        // Para isso, você precisará refatorar o código original para aceitar um objeto mysqli como parâmetro ou usar outra técnica de injeção de dependência.

        // Supondo que você tenha uma função que aceita um objeto mysqli como parâmetro:
        $resultados = $this->runQueries($mysqliMock);

        // Verificar os resultados
        $this->assertArrayHasKey('realtime', $resultados);
        $this->assertArrayHasKey('bueiros', $resultados);
        $this->assertArrayHasKey('combinado', $resultados);
        
        $this->assertCount(1, $resultados['realtime']);
        $this->assertCount(1, $resultados['bueiros']);
        $this->assertCount(1, $resultados['combinado']);
        
        $this->assertEquals('Precisa de Atenção', $resultados['combinado'][0]['nivel_detritos']);
    }

    private function runQueries($mysqli)
    {
        // A função que contém o código original que estamos testando,
        // refatorada para aceitar o objeto mysqli como parâmetro.
        // Este é um exemplo de como a função pode ser:
        
        $sql_realtime = "SELECT * FROM sensor_data";
        $result_realtime = $mysqli->query($sql_realtime);

        $sql_bueiros = "SELECT * FROM bueiros";
        $result_bueiros = $mysqli->query($sql_bueiros);

        $sql_combinado = "SELECT sd.id_medicao, sd.id_sensor, sd.id_bueiro, sd.distancia, sd.timestamp, b.profundidade, b.localizacao FROM sensor_data sd INNER JOIN bueiros b ON sd.id_bueiro = b.id_bueiro";
        $result_combinado = $mysqli->query($sql_combinado);

        $resultados = array(
            "realtime" => $result_realtime->fetch_all(MYSQLI_ASSOC),
            "bueiros" => $result_bueiros->fetch_all(MYSQLI_ASSOC),
            "combinado" => array()
        );

        if ($result_combinado->num_rows > 0) {
            while ($row = $result_combinado->fetch_assoc()) {
                $percentual = (50 - $row['distancia']) / 20 * 100;

                if ($percentual > 75) {
                    $nivel_detritos = 'Vazio';
                } elseif ($percentual >= 40 && $percentual <= 75) {
                    $nivel_detritos = 'Precisa de Atenção';
                } else {
                    $nivel_detritos = 'Entupido';
                }

                $row['nivel_detritos'] = $nivel_detritos;
                $resultados['combinado'][] = $row;
            }
        }

        return $resultados;
    }
}
