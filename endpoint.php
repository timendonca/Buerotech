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

// Desabilitar temporariamente a verificação de chaves estrangeiras
$mysqli->query("SET FOREIGN_KEY_CHECKS=0");

// Verificar se há dados POST recebidos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar a distância do POST
    $distance = $_POST['distance'];

    // IDs do sensor e do bueiro (adicionados manualmente)
    $id_sensor = 1; // Defina o ID do sensor
    $id_bueiro = 1; // Defina o ID do bueiro

    // Preparar a consulta SQL para inserção
    $sql = "INSERT INTO sensor_data (id_sensor, id_bueiro, distancia) VALUES ($id_sensor, $id_bueiro, $distance)";

    // Executar a consulta SQL
    if ($mysqli->query($sql) === TRUE) {
        echo "Dados inseridos com sucesso!";

        // Verificar se há mais de 20 resultados
        $sql_count = "SELECT COUNT(*) AS total FROM sensor_data";
        $result_count = $mysqli->query($sql_count);
        $total_rows = $result_count->fetch_assoc()['total'];

        // Se houver mais de 10 resultados, remover os mais antigos
        if ($total_rows > 10) {
            $sql_delete = "DELETE FROM sensor_data ORDER BY timestamp ASC LIMIT ?";
            $stmt_delete = $mysqli->prepare($sql_delete);
            $limit = $total_rows - 10;
            $stmt_delete->bind_param("i", $limit);
            $stmt_delete->execute();
        }

    } else {
        echo "Erro ao inserir dados: " . $mysqli->error;
    }
} else {
    // Se não houver dados POST, retornar erro
    http_response_code(400);
    echo "Requisição inválida.";
}

// Habilitar novamente a verificação de chaves estrangeiras
$mysqli->query("SET FOREIGN_KEY_CHECKS=1");

// Fechar a conexão
$mysqli->close();
?>
