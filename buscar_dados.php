<?php
// Configurações do banco de dados MySQL
$mysqlHostname = 'mysql3.serv00.com'; // Hostname do servidor MySQL
$mysqlUsername = 'm2486_Kblo'; // Nome de usuário do MySQL
$mysqlPassword = 'Mongo1!'; // Senha do MySQL
$mysqlDatabase = 'm2486_Kblo'; // Nome do banco de dados MySQL

// Conectar ao servidor MySQL
$mysqli = new mysqli($mysqlHostname, $mysqlUsername, $mysqlPassword, $mysqlDatabase);

// Verificar conexão
if ($mysqli->connect_error) {
    die("Conexão falhou: " . $mysqli->connect_error);
}

// Consulta SQL para buscar os dados em tempo real
$sql_realtime = "SELECT * FROM sensor_data";
$result_realtime = $mysqli->query($sql_realtime);

// Consulta SQL para buscar os bueiros cadastrados
$sql_bueiros = "SELECT * FROM bueiros";
$result_bueiros = $mysqli->query($sql_bueiros);

// Consulta SQL para buscar os dados combinados
$sql_combinado = "SELECT sd.id_medicao, sd.id_sensor, sd.id_bueiro, sd.distancia, sd.timestamp, b.profundidade, b.localizacao
                 FROM sensor_data sd
                 INNER JOIN bueiros b ON sd.id_bueiro = b.id_bueiro";
$result_combinado = $mysqli->query($sql_combinado);

// Array para armazenar os resultados
$resultados = array(
    "realtime" => $result_realtime->fetch_all(MYSQLI_ASSOC),
    "bueiros" => $result_bueiros->fetch_all(MYSQLI_ASSOC),
    "combinado" => array() // Inicializar a chave 'combinado'
);

// Verificar se há resultados combinados
if ($result_combinado->num_rows > 0) {
    // Loop através dos resultados combinados
    while ($row = $result_combinado->fetch_assoc()) {
        // Calcular o nível de detritos
        $nivel_detritos = '';

        // Converter a distância para percentual
        $percentual = (50 - $row['distancia']) / 20 * 100;

        if ($percentual > 75) { // Quanto maior a porcentagem, mais cheio está
            $nivel_detritos = 'Vazio';
        } elseif ($percentual >= 40 && $percentual <= 75) {
            $nivel_detritos = 'Precisa de Atenção';
        } elseif ($percentual < 40) {
            $nivel_detritos = 'Entupido';
        }

        // Adicionar os dados e o nível de detritos ao array de resultados
        $row['nivel_detritos'] = $nivel_detritos;
        $resultados['combinado'][] = $row;
    }
}


// Fechar a conexão
$mysqli->close();

// Retornar os resultados como JSON
echo json_encode($resultados);
?>
