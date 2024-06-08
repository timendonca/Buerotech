<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="20"> <!-- Atualiza a página a cada 25 segundos -->
    <title>Dados de Monitoramento</title>
    <!-- Link para o arquivo CSS -->
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script>
        // Função para iniciar o contador de 30 segundos
        function iniciarContador() {
            var segundos = 20;
            var contador = setInterval(function() {
                document.getElementById('contador').innerText = 'Próxima atualização em ' + segundos + ' segundos';
                segundos--;

                // Se o contador chegar a 0, reinicia
                if (segundos < 0) {
                    clearInterval(contador);
                    iniciarContador(); // Reinicia o contador
                }
            }, 1000); // Atualiza a cada 1 segundo
        }

        // Iniciar o contador quando a página carregar
        window.onload = function() {
            iniciarContador();
            buscarDados();
        };

        // Função para buscar dados do servidor
        function buscarDados() {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var dados = JSON.parse(xhr.responseText);
                    mostrarDados(dados);
                }
            };
            xhr.open("GET", "buscar_dados.php", true);
            xhr.send();
        }

        // Função para exibir os dados nas tabelas
        function mostrarDados(dados) {
            var tabelaRealtime = document.getElementById("tabelaRealtime");
            var tabelaBueiros = document.getElementById("tabelaBueiros");

            // Limpar o conteúdo das tabelas
            tabelaRealtime.innerHTML = "";
            tabelaBueiros.innerHTML = "";

            // Cabeçalho das tabelas
            var cabecalhoRealtime = "<thead><tr><th>ID Medição</th><th>ID Sensor</th><th>ID Bueiro</th><th>Distância (cm)</th><th>Timestamp</th></tr></thead>";
            var cabecalhoBueiros = "<thead><tr><th>ID Bueiro</th><th>Localização</th><th>Profundidade</th><th>Nível de Detritos</th></tr></thead>";

            // Preencher a tabela de dados em tempo real
            tabelaRealtime.innerHTML += cabecalhoRealtime;
            dados.realtime.forEach(function(row) {
                var linha = "<tr>";
                linha += "<td>" + row.id_medicao + "</td>";
                linha += "<td>" + row.id_sensor + "</td>";
                linha += "<td>" + row.id_bueiro + "</td>";
                linha += "<td>" + row.distancia + "</td>";
                linha += "<td>" + row.timestamp + "</td>";
                linha += "</tr>";
                tabelaRealtime.innerHTML += linha;
            });

            // Preencher a tabela de bueiros cadastrados
            tabelaBueiros.innerHTML += "<caption><h2>Bueiros Cadastrados</h2></caption>";
            tabelaBueiros.innerHTML += cabecalhoBueiros;
            dados.combinado.forEach(function(row) { // Usar dados.combinado para calcular o nível de detritos
                var linha = "<tr>";
                linha += "<td>" + row.id_bueiro + "</td>";
                linha += "<td>" + row.localizacao + "</td>";
                linha += "<td>" + row.profundidade + "</td>";

                // Exibir o nível de detritos diretamente do servidor
                linha += "<td>" + row.nivel_detritos + "</td>";

                linha += "</tr>";
                tabelaBueiros.innerHTML += linha;
            });
        }
    </script>
</head>
<body>

<h2>Dados do Sensor Em Tempo Real</h2>

<!-- Div para exibir o contador -->
<div id="contador"></div>

<!-- Tabela para os dados em tempo real -->
<table id="tabelaRealtime">
    <caption>Dados em Tempo Real</caption>
</table>

<!-- Tabela para os bueiros cadastrados -->
<table id="tabelaBueiros">
</table>

</body>
</html>
