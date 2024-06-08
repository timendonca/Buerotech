#include <ESP8266WiFi.h>
#include <HCSR04.h>
#include <ESP8266HTTPClient.h>

/*const char* ssid = "kblo";
const char* password = "16071986";
const char* serverUrl = "http://kblo.serv00.net/arduino/endpoint.php";*/

/*const char* ssid = "DSM_FATEC";
const char* password = "4semestre";
const char* serverUrl = "http://kblo.serv00.net/arduino/endpoint.php";*/

const char* ssid = "Galaxy M51";
const char* password = "12345678";
const char* serverUrl = "http://kblo.serv00.net/arduino/endpoint.php";

/*const char* ssid = "Fatec24GHz";
const char* password = "fatecitapira";
const char* serverUrl = "http://kblo.serv00.net/arduino/endpoint.php";*/

#define pino_trigger D5
#define pino_echo D6

UltraSonicDistanceSensor distanceSensor(pino_trigger, pino_echo);

WiFiClient client; // Declaração global da variável WiFiClient

void setup() {
  Serial.begin(9600);
  delay(10);

  // Conectar ao Wi-Fi
  Serial.println();
  Serial.println();
  Serial.print("Conectando a ");
  Serial.println(ssid);

  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("");
  Serial.println("WiFi conectado");
  Serial.println("Endereço IP: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  float distance_cm = distanceSensor.measureDistanceCm();

  // Construa a string com os dados que deseja enviar
  String postData = "distance=" + String(distance_cm);

  // Iniciar conexão HTTP
  HTTPClient http;

  Serial.print("[HTTP] Iniciando POST...");
  // Use ::begin(WiFiClient, url) em vez de ::begin(url)
  if (http.begin(client, serverUrl)) { 
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    // Envia o POST e obtém a resposta
    int httpCode = http.POST(postData);

    // Verifica se a requisição foi bem sucedida
    if (httpCode > 0) {
      Serial.printf("[HTTP] POST realizado com sucesso, código: %d\n", httpCode);
      String payload = http.getString();
      Serial.println("Resposta do servidor: " + payload);
    } else {
      Serial.printf("[HTTP] POST falhou, código de erro: %d\n", httpCode);
    }

    // Libera os recursos
    http.end();
  } else {
    Serial.println("Falha ao conectar ao servidor.");
  }

  delay(10000); // Intervalo de 10 segundos entre as leituras
}
