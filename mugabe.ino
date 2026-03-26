

#include <WiFi.h>
#include <PubSubClient.h>
#include <ArduinoJson.h>

const char* ssid = "";
const char* password ="";

// MQTT broker setti    bvvrtyuiytrengs
const char* mqttServer = "broker.hivemq.com";
const int mqttPort = 1883;
const char* mqttUser = "";
const char* mqttPassword = "";

// MQTT topics
const char* topicSubscribe = "esp32/demo/in";
const char* topicPublish   = "esp32/demo/out";

// Globals
WiFiClient espClient;
PubSubClient client(espClient);

unsigned long lastPublish = 0;
const unsigned long publishInterval = 5000;

bool ledState = false;
int counter = 0;

// Connect to Wi-Fi
void connectWiFi() {
  Serial.print("Connecting to Wi-Fi");

  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println();
  Serial.println("Wi-Fi connected");
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());
}

// MQTT callback
void mqttCallback(char* topic, byte* payload, unsigned int length) {
  Serial.println();
  Serial.print("Message received on topic: ");
  Serial.println(topic);

  char message[length + 1];
  for (unsigned int i = 0; i < length; i++) {
    message[i] = (char)payload[i];
  }
  message[length] = '\0'; // fixed here

  Serial.print("Raw payload: ");
  Serial.println(message);

  // FIXED: use StaticJsonDocument
  StaticJsonDocument<256> doc;
  DeserializationError error = deserializeJson(doc, message);

  if (error) {
    Serial.print("JSON parse failed: ");
    Serial.println(error.c_str());
    return;
  }

  const char* command = doc["command"] | "";
  int value = doc["value"] | 0;
  const char* device = doc["device"] | "unknown";

  Serial.println(device);
  Serial.println(command);
  Serial.println(value);

  if (strcmp(command, "led_on") == 0) {
    ledState = true;
    digitalWrite(2, HIGH);
  } 
  else if (strcmp(command, "led_off") == 0) {
    ledState = false;
    digitalWrite(2, LOW);
  }
  else if (strcmp(command, "set_counter") == 0) {
    counter = value;
  }
}

// Connect MQTT
void connectMQTT() {
  while (!client.connected()) {
    Serial.print("Connecting to MQTT...");

    String clientId = "ESP32Client-";
    clientId += String((uint32_t)ESP.getEfuseMac(), HEX);

    bool connected;
    if (strlen(mqttUser) > 0) {
      connected = client.connect(clientId.c_str(), mqttUser, mqttPassword);
    } else {
      connected = client.connect(clientId.c_str());
    }

    if (connected) {
      Serial.println("connected");
      client.subscribe(topicSubscribe);
    } else {
      Serial.print("failed, rc=");
      Serial.println(client.state());
      delay(3000);
    }
  }
}

// Publish JSON
void publishJsonMessage() {
  // FIXED: use StaticJsonDocument
  StaticJsonDocument<256> doc;

  doc["device"] = "esp32";
  doc["status"] = ledState ? "on" : "off";
  doc["counter"] = counter;
  doc["ip"] = WiFi.localIP().toString();
  doc["rssi"] = WiFi.RSSI();

  char buffer[256];
  size_t n = serializeJson(doc, buffer);

  client.publish(topicPublish, buffer, n);

  Serial.println(buffer);
}

// Setup
void setup() {
  Serial.begin(115200);
  pinMode(2, OUTPUT);
  digitalWrite(2, LOW);

  connectWiFi();

  client.setServer(mqttServer, mqttPort);
  client.setCallback(mqttCallback);
}

// Loop
void loop() {
  if (WiFi.status() != WL_CONNECTED) {
    connectWiFi();
  }

  if (!client.connected()) {
    connectMQTT();
  }

  client.loop();

  if (millis() - lastPublish >= publishInterval) {
    lastPublish = millis();
    counter++;
    publishJsonMessage();
  }
