/*
  ===========================================================================
  PROJECT: SMART WATER MONITORING SYSTEM (ESP32 MASTER)
  STRUCTURE: REFACTORED & CLEAN
  FEATURES: 
    - System ON/OFF Control (Switch Logic)
    - Auto Loop & Standby Timer
    - Manual Override Popups
    - WiFi, MQTT, TFT, Keypad
  ===========================================================================
*/

#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <PubSubClient.h>
#include <ArduinoJson.h>
#include <TFT_eSPI.h> 
#include <SPI.h>
#include <Keypad.h>

// =========================================================================
// 1. KONFIGURASI & DEFINISI
// =========================================================================

// --- TAMPILAN ---
#define TFT_GREY 0x5AEB 

// --- JARINGAN ---
const char* ssid        = "Kutabaca 2";
const char* password    = ""; 

const char* mqtt_server = "ge111de0.ala.asia-southeast1.emqxsl.com";
const int mqtt_port     = 8883;
const char* mqtt_user   = "aufa"; 
const char* mqtt_pass   = "aufa"; 
const char* mqtt_topic  = "sensor/air";

// --- KEYPAD MAPPING ---
const byte ROWS = 4; 
const byte COLS = 4; 
char hexaKeys[ROWS][COLS] = {
  {'1','2','3','A'},
  {'4','5','6','B'},
  {'7','8','9','C'},
  {'*','0','#','D'} 
};
byte rowPins[ROWS] = {15, 16, 32, 33}; 
byte colPins[COLS] = {25, 26, 27, 14}; 

// =========================================================================
// 2. OBJEK GLOBAL & VARIABEL
// =========================================================================

// --- Objek Hardware ---
WiFiClientSecure espClient;
PubSubClient client(espClient);
TFT_eSPI tft = TFT_eSPI(); 
Keypad customKeypad = Keypad(makeKeymap(hexaKeys), rowPins, colPins, ROWS, COLS);

// --- Variabel Data Sensor ---
String glb_Status = "SYSTEM OFF"; 
String glb_pH     = "0";
String glb_Turb   = "0";
String glb_TDS    = "0";
int glb_LvlChamber = 0;
int glb_LvlAqua    = 0;
int glb_Detik      = 0; 

// --- Variabel Konfigurasi ---
String inputBuffer = "";
int set_Interval   = 15; // Menit
int set_Durasi     = 60; // Detik

// --- SYSTEM STATE FLAGS (LOGIKA UTAMA) ---
unsigned long standbyStartMillis = 0; 
bool isSystemRunning = false; // False = OFF (Mati Total), True = ON (Looping)
bool isStandbyMode   = false; // False = Aktif Monitoring, True = Menunggu (Timer)

// --- Chart Buffer ---
float chartPH[30];
float chartTurb[30];
float chartTDS[30];

// --- UI Navigation State ---
enum ScreenState { 
  DASHBOARD, 
  MENU_MAIN, 
  MENU_SETTINGS, 
  LEVEL_MONITOR, 
  SETTING_INTERVAL, 
  SETTING_DURASI, 
  CONFIRM_SETTING, 
  CHART_VIEW,
  POPUP_START, // Popup untuk Menyalakan
  POPUP_STOP   // Popup untuk Mematikan
};

ScreenState currentScreen = DASHBOARD;
int menuIndex = 0;
int settingMenuIndex = 0;

// =========================================================================
// 3. FUNCTION PROTOTYPES
// =========================================================================
// System & Network
void setup_wifi();
void reconnect();
void publishDataMQTT();
void sendConfigToArduino();
void triggerArduinoStart();
void parseSerialData(String data);

// Logic & UI
void handleInput(char key);
void updateCharts(float ph, float turb, float tds);

// Graphics / Display
void drawDashboardLayout();
void updateDashboardValues();
void drawMenuMain();
void drawMenuSettings();
void drawLevelMonitor();
void drawSettingScreen(String title, String inputVal, String unit, int oldVal);
void drawConfirmation();
void drawChartView();
void drawPopupStart();
void drawPopupStop();
void drawCleanValue(int x, int y, String val, uint16_t color, int wBox);


// =========================================================================
// 4. MAIN SETUP
// =========================================================================
void setup() {
  Serial.begin(115200); 
  Serial.setTimeout(50); 
  customKeypad.setDebounceTime(50);
  
  // Init Layar
  tft.init();
  tft.setRotation(1);
  tft.fillScreen(TFT_BLACK);

  // Koneksi WiFi
  tft.setTextColor(TFT_WHITE, TFT_BLACK);
  tft.drawCentreString("CONNECTING WIFI...", 160, 100, 2);
  setup_wifi();

  // Init MQTT SSL
  espClient.setInsecure(); 
  client.setServer(mqtt_server, mqtt_port);

  // Init Data Chart (Nol semua)
  for(int i=0; i<30; i++) { 
    chartPH[i]=0; chartTurb[i]=0; chartTDS[i]=0; 
  }

  // --- KONDISI AWAL: MATI (OFF) ---
  isSystemRunning = false;
  isStandbyMode = false;
  glb_Status = "SYSTEM OFF";

  // Gambar Dashboard
  drawDashboardLayout();
  updateDashboardValues();
}

// =========================================================================
// 5. MAIN LOOP
// =========================================================================
void loop() {
  // 1. MQTT Keep Alive
  if (!client.connected()) {
    reconnect();
  }
  client.loop();

  // 2. Baca Keypad
  char key = customKeypad.getKey();
  if (key) {
    handleInput(key);
  }

  // 3. LOGIKA TIMER OTOMATIS
  // Hanya berjalan jika Sistem ON (Running) DAN sedang dalam Mode Standby
  if (isSystemRunning && isStandbyMode) {
    unsigned long currentMillis = millis();
    unsigned long intervalMillis = (unsigned long)set_Interval * 60 * 1000;
    
    // Jika waktu tunggu habis -> Trigger Monitoring Baru
    if (currentMillis - standbyStartMillis >= intervalMillis) {
      triggerArduinoStart(); 
    }
  }

  // 4. KOMUNIKASI SERIAL DENGAN ARDUINO
  if (Serial.available() > 0) {
    String data = Serial.readStringUntil('\n');
    data.trim();
    
    if(data.length() > 5) {
      
      // A. Jika menerima sinyal "Monitoring:ready" (Siklus Selesai)
      if (data.indexOf("Monitoring:ready") >= 0) {
         if (isSystemRunning) {
             // Jika Sistem ON: Masuk Standby & Mulai Timer
             glb_Status = "STANDBY";
             isStandbyMode = true; 
             standbyStartMillis = millis();
         } else {
             // Jika Sistem OFF: Tetap diam
             glb_Status = "SYSTEM OFF";
             isStandbyMode = false;
         }
         
         if (currentScreen == DASHBOARD) updateDashboardValues();
      }
      
      // B. Jika menerima Log Status [STATUS] ...
      else if(data.startsWith("[")) {
         if(data.indexOf("STATUS") > 0) {
            String rawStatus = data.substring(data.indexOf("]")+2);
            if(rawStatus.length() > 20) rawStatus = rawStatus.substring(0,20);
            
            // Hanya update status jika Sistem ON
            if (isSystemRunning) {
                glb_Status = rawStatus;
                // Jika Arduino sedang sibuk (bukan Standby), matikan timer
                if(glb_Status.indexOf("STANDBY") < 0) {
                  isStandbyMode = false;
                }
            }
            
            if(currentScreen == DASHBOARD) updateDashboardValues();
         }
      } 
      
      // C. Jika menerima Data Sensor (CSV)
      else {
         parseSerialData(data);
      }
    }
  }
}

// =========================================================================
// 6. INPUT HANDLER (LOGIKA MENU & POPUP)
// =========================================================================
void handleInput(char key) {
  // Tombol 'A' selalu kembali ke Dashboard
  if (key == 'A') { 
    currentScreen = DASHBOARD; 
    drawDashboardLayout(); 
    updateDashboardValues(); 
    return;
  }

  switch (currentScreen) {
    // --- DASHBOARD ---
    case DASHBOARD:
      if (key == 'B') { 
        currentScreen = MENU_MAIN; 
        menuIndex = 0; 
        drawMenuMain(); 
      }
      
      // TOMBOL 'D': Logic Penentu Popup (Start/Stop)
      if (key == 'D') {
        if (isSystemRunning) {
            // Jika sedang jalan -> Tawarkan STOP
            currentScreen = POPUP_STOP;
            drawPopupStop();
        } else {
            // Jika sedang mati -> Tawarkan START
            currentScreen = POPUP_START;
            drawPopupStart();
        }
      }
      break;

    // --- POPUP START (Saat kondisi OFF) ---
    case POPUP_START:
      if (key == '#') { 
        // USER PILIH: YA, MULAI
        isSystemRunning = true; // Nyalakan Sistem
        
        tft.fillScreen(TFT_BLACK); 
        tft.setTextColor(TFT_GREEN); 
        tft.drawCentreString("STARTING LOOP...", 160, 100, 4);
        
        triggerArduinoStart(); // Perintahkan Arduino
        delay(1000);
        
        currentScreen = DASHBOARD; 
        drawDashboardLayout(); 
        updateDashboardValues();
      } 
      else if (key == '*') {
        // USER PILIH: BATAL
        currentScreen = DASHBOARD; 
        drawDashboardLayout(); 
        updateDashboardValues();
      }
      break;

    // --- POPUP STOP (Saat kondisi ON) ---
    case POPUP_STOP:
      if (key == '#') { 
        // USER PILIH: YA, MATIKAN
        isSystemRunning = false; // Matikan Sistem
        isStandbyMode = false;
        glb_Status = "SYSTEM OFF";
        
        tft.fillScreen(TFT_BLACK); 
        tft.setTextColor(TFT_RED); 
        tft.drawCentreString("SYSTEM OFF", 160, 100, 4);
        delay(1000);
        
        currentScreen = DASHBOARD; 
        drawDashboardLayout(); 
        updateDashboardValues();
      } 
      else if (key == '*') {
        // USER PILIH: BATAL (Lanjut jalan)
        currentScreen = DASHBOARD; 
        drawDashboardLayout(); 
        updateDashboardValues();
      }
      break;

    // --- MENU UTAMA ---
    case MENU_MAIN:
      if (key == '2') { menuIndex--; if(menuIndex<0) menuIndex=2; drawMenuMain(); }
      else if (key == '8') { menuIndex++; if(menuIndex>2) menuIndex=0; drawMenuMain(); }
      else if (key == '#') { 
        if(menuIndex==0) { currentScreen=LEVEL_MONITOR; drawLevelMonitor(); }
        if(menuIndex==1) { currentScreen=MENU_SETTINGS; settingMenuIndex=0; drawMenuSettings(); }
        if(menuIndex==2) { currentScreen=CHART_VIEW; drawChartView(); }
      }
      break;

    // --- MENU SETTINGS ---
    case MENU_SETTINGS:
      if (key == '2') { settingMenuIndex--; if(settingMenuIndex<0) settingMenuIndex=2; drawMenuSettings(); }
      else if (key == '8') { settingMenuIndex++; if(settingMenuIndex>2) settingMenuIndex=0; drawMenuSettings(); }
      else if (key == '#') {
        if (settingMenuIndex == 0) { currentScreen=SETTING_INTERVAL; inputBuffer=""; drawSettingScreen("SET INTERVAL", "", "Menit", set_Interval); }
        else if (settingMenuIndex == 1) { currentScreen=SETTING_DURASI; inputBuffer=""; drawSettingScreen("SET DURASI", "", "Detik", set_Durasi); }
        else if (settingMenuIndex == 2) { currentScreen=CONFIRM_SETTING; drawConfirmation(); }
      }
      break;

    // --- INPUT INTERVAL ---
    case SETTING_INTERVAL:
      if (key>='0' && key<='9') { inputBuffer+=key; drawSettingScreen("SET INTERVAL", inputBuffer, "Menit", set_Interval); }
      else if (key=='C') { inputBuffer=""; drawSettingScreen("SET INTERVAL", inputBuffer, "Menit", set_Interval); }
      else if (key=='#') { if(inputBuffer.length()>0) set_Interval=inputBuffer.toInt(); currentScreen=MENU_SETTINGS; drawMenuSettings(); }
      break;

    // --- INPUT DURASI ---
    case SETTING_DURASI:
      if (key>='0' && key<='9') { inputBuffer+=key; drawSettingScreen("SET DURASI", inputBuffer, "Detik", set_Durasi); }
      else if (key=='C') { inputBuffer=""; drawSettingScreen("SET DURASI", inputBuffer, "Detik", set_Durasi); }
      else if (key=='#') { if(inputBuffer.length()>0) set_Durasi=inputBuffer.toInt(); currentScreen=MENU_SETTINGS; drawMenuSettings(); }
      break;

    // --- KONFIRMASI SETTING ---
    case CONFIRM_SETTING:
      if (key == '#') { 
        tft.fillScreen(TFT_BLACK); tft.setTextColor(TFT_GREEN); tft.drawCentreString("SAVED!", 160, 100, 4);
        sendConfigToArduino(); 
        delay(1000); 
        currentScreen=DASHBOARD; drawDashboardLayout(); updateDashboardValues();
      }
      else if (key == '*') { currentScreen=MENU_SETTINGS; drawMenuSettings(); }
      break;
  }
}

// =========================================================================
// 7. SYSTEM & NETWORK FUNCTIONS
// =========================================================================

void setup_wifi() {
  Serial.print("Connecting"); 
  WiFi.begin(ssid, password);
  
  int r=0; 
  while(WiFi.status()!=WL_CONNECTED && r<20){
    delay(500); Serial.print("."); r++;
  }
  
  if(WiFi.status()==WL_CONNECTED){
    tft.fillScreen(TFT_BLACK); tft.setTextColor(TFT_GREEN); tft.drawCentreString("WIFI OK",160,100,2);
    delay(1000);
  } else {
    tft.fillScreen(TFT_BLACK); tft.setTextColor(TFT_RED); tft.drawCentreString("WIFI FAIL",160,100,2);
    delay(1000);
  }
}

void reconnect() {
  if(WiFi.status()!=WL_CONNECTED) WiFi.begin(ssid,password);
  
  if(!client.connected()){
    String id = "ESP" + String(random(0xffff));
    client.connect(id.c_str(), mqtt_user, mqtt_pass);
  }
}

void parseSerialData(String data) {
  // Format: pH,Turb,TDS,LvlChamber,LvlAqua,Detik,Status
  int i1 = data.indexOf(','); 
  int i2 = data.indexOf(',', i1+1); 
  int i3 = data.indexOf(',', i2+1); 
  int i4 = data.indexOf(',', i3+1); 
  int i5 = data.indexOf(',', i4+1); 
  int i6 = data.indexOf(',', i5+1); 
  
  if(i6 > 0){
    glb_pH = data.substring(0, i1);
    glb_Turb = data.substring(i1+1, i2);
    glb_TDS = data.substring(i2+1, i3);
    glb_LvlChamber = data.substring(i3+1, i4).toInt();
    glb_LvlAqua = data.substring(i4+1, i5).toInt();
    glb_Detik = data.substring(i5+1, i6).toInt();
    
    // Simpan status tapi jangan timpa kalau sedang System OFF (kecuali bacaan sensor)
    String rxStatus = data.substring(i6+1);
    if(isSystemRunning) {
        glb_Status = rxStatus;
    }

    updateCharts(glb_pH.toFloat(), glb_Turb.toFloat(), glb_TDS.toFloat());

    // Update UI Sesuai Halaman
    if(currentScreen == DASHBOARD) updateDashboardValues(); 
    else if(currentScreen == LEVEL_MONITOR) drawLevelMonitor(); 
    else if(currentScreen == CHART_VIEW) drawChartView();

    publishDataMQTT();
  }
}

void publishDataMQTT() {
  StaticJsonDocument<512> doc;
  
  JsonObject s = doc.createNestedObject("sensors");
  s["ph"]   = glb_pH.toFloat(); 
  s["tds"]  = glb_TDS.toInt(); 
  s["turb"] = glb_Turb.toFloat(); 
  s["tank"] = String(glb_LvlAqua) + "%"; 
  s["cham"] = String(glb_LvlChamber) + "%";
  
  doc["status"] = glb_Status;
  
  // Kirim info timer
  if(isSystemRunning && isStandbyMode){
      long rem = ((long)set_Interval * 60000) - (millis() - standbyStartMillis);
      if(rem < 0) rem = 0;
      doc["timer_info"] = String(rem/1000) + "s next";
  } else if(isSystemRunning) {
      doc["timer_info"] = String(glb_Detik) + "s run";
  } else {
      doc["timer_info"] = "OFF";
  }

  char buffer[512]; 
  serializeJson(doc, buffer);
  
  if(client.connected()) client.publish(mqtt_topic, buffer);
}

void triggerArduinoStart() {
  Serial.println("Monitoring:" + String(set_Durasi));
  
  isSystemRunning = true;
  isStandbyMode = false;
  glb_Status = "STARTING...";
  glb_Detik = 0;
  
  if(currentScreen == DASHBOARD) updateDashboardValues();
}

void sendConfigToArduino() {
  Serial.println("SET:" + String(set_Interval) + ":" + String(set_Durasi));
}

void updateCharts(float ph, float turb, float tds) {
  for(int i=0; i<29; i++){
    chartPH[i]   = chartPH[i+1];
    chartTurb[i] = chartTurb[i+1];
    chartTDS[i]  = chartTDS[i+1];
  }
  chartPH[29]   = ph;
  chartTurb[29] = turb;
  chartTDS[29]  = tds;
}

// =========================================================================
// 8. GRAPHICS & DRAWING FUNCTIONS
// =========================================================================

void drawDashboardLayout() {
  tft.fillScreen(TFT_BLACK); 
  tft.fillRect(0,0,320,30,TFT_NAVY); 
  tft.setTextColor(TFT_WHITE,TFT_NAVY); 
  tft.drawCentreString("MONITORING AIR",160,5,4); 
  
  tft.fillRect(0,30,320,20,TFT_DARKGREY); 
  
  tft.setTextColor(TFT_CYAN,TFT_BLACK);   tft.drawString("pH:",20,70,4); 
  tft.setTextColor(TFT_ORANGE,TFT_BLACK); tft.drawString("Turb:",20,110,4); 
  tft.setTextColor(TFT_GREEN,TFT_BLACK);  tft.drawString("TDS:",20,150,4); 
  
  tft.setTextColor(TFT_WHITE,TFT_BLACK); 
  tft.drawString("Cham:",180,80,2); 
  tft.drawString("Aqua:",180,120,2); 
  
  // Status WiFi
  if(WiFi.status()==WL_CONNECTED){
    tft.setTextColor(TFT_GREEN,TFT_BLACK); tft.drawString("WIFI: ON",250,220,1);
  }else{
    tft.setTextColor(TFT_RED,TFT_BLACK); tft.drawString("WIFI: OFF",250,220,1);
  }
  
  tft.setTextColor(TFT_LIGHTGREY,TFT_BLACK); 
  tft.drawCentreString("[B] MENU   [D] POWER",160,210,2);
}

void updateDashboardValues() {
  tft.fillRect(0,30,320,20,TFT_DARKGREY); 
  tft.setTextColor(TFT_YELLOW,TFT_DARKGREY); 
  
  String tStr = "---";
  
  if (!isSystemRunning) {
    tStr = "OFF";
  } else if (isStandbyMode) {
    long rem = ((long)set_Interval * 60000) - (millis() - standbyStartMillis);
    if(rem < 0) rem = 0;
    tStr = "Next: " + String((rem/60000)+1) + "m";
  } else {
    tStr = "Run: " + String(glb_Detik) + "s";
  }
  
  tft.setTextDatum(ML_DATUM); tft.drawString(glb_Status,10,40,2);
  tft.setTextDatum(MR_DATUM); tft.drawString(tStr,310,40,2);
  
  drawCleanValue(80,70,glb_pH,TFT_CYAN,90);
  drawCleanValue(100,110,glb_Turb,TFT_ORANGE,90);
  drawCleanValue(100,150,glb_TDS,TFT_GREEN,90);
  drawCleanValue(240,80,String(glb_LvlChamber)+"%",TFT_WHITE,60);
  drawCleanValue(240,120,String(glb_LvlAqua)+"%",TFT_WHITE,60);
}

void drawPopupStart() {
  tft.fillScreen(TFT_DARKGREEN); 
  tft.fillRect(20, 40, 280, 160, TFT_BLACK); 
  tft.drawRect(20, 40, 280, 160, TFT_WHITE);

  tft.setTextColor(TFT_GREEN, TFT_BLACK); 
  tft.drawCentreString("SYSTEM IS OFF", 160, 60, 4);

  tft.setTextColor(TFT_WHITE, TFT_BLACK); 
  tft.drawCentreString("Mulai siklus monitoring?", 160, 100, 2);
  
  tft.setTextColor(TFT_YELLOW, TFT_BLACK); 
  tft.drawString("[#] MULAI & LOOPING", 40, 140, 2);
  
  tft.setTextColor(TFT_CYAN, TFT_BLACK); 
  tft.drawString("[*] BATAL (TETAP OFF)", 40, 170, 2);
}

void drawPopupStop() {
  tft.fillScreen(TFT_MAROON); 
  tft.fillRect(20, 40, 280, 160, TFT_BLACK); 
  tft.drawRect(20, 40, 280, 160, TFT_WHITE);

  tft.setTextColor(TFT_RED, TFT_BLACK); 
  tft.drawCentreString("SYSTEM IS ON", 160, 60, 4);

  tft.setTextColor(TFT_WHITE, TFT_BLACK); 
  tft.drawCentreString("Matikan monitoring?", 160, 100, 2);
  
  tft.setTextColor(TFT_RED, TFT_BLACK); 
  tft.drawString("[#] MATIKAN (OFF)", 40, 140, 2);
  
  tft.setTextColor(TFT_GREEN, TFT_BLACK); 
  tft.drawString("[*] BATAL (LANJUT)", 40, 170, 2);
}

void drawCleanValue(int x, int y, String val, uint16_t c, int w) {
  tft.fillRect(x, y, w, 28, TFT_BLACK); 
  tft.setTextDatum(TL_DATUM); 
  tft.setTextColor(c, TFT_BLACK); 
  tft.drawString(val, x, y, 4); 
}

void drawMenuMain() {
  tft.fillScreen(TFT_BLACK); 
  tft.setTextColor(TFT_YELLOW); 
  tft.drawCentreString("MAIN MENU", 160, 20, 4);
  
  String m[] = {"1. LEVEL MONITOR", "2. PENGATURAN", "3. CHARTS"};
  for(int i=0; i<3; i++){
    if(i == menuIndex) { 
      tft.fillRect(30, 80+(i*40), 260, 35, TFT_WHITE); 
      tft.setTextColor(TFT_BLACK, TFT_WHITE); 
    } else { 
      tft.drawRect(30, 80+(i*40), 260, 35, TFT_WHITE); 
      tft.setTextColor(TFT_WHITE, TFT_BLACK); 
    }
    tft.drawString(m[i], 40, 85+(i*40), 2);
  }
  tft.setTextColor(TFT_LIGHTGREY, TFT_BLACK); 
  tft.drawCentreString("2:UP 8:DOWN #:OK A:BACK", 160, 220, 2);
}

void drawMenuSettings() {
  tft.fillScreen(TFT_BLACK); 
  tft.setTextColor(TFT_YELLOW); 
  tft.drawCentreString("PENGATURAN", 160, 20, 4);
  
  String m[3];
  m[0] = "1. Interval (" + String(set_Interval) + "m)";
  m[1] = "2. Durasi   (" + String(set_Durasi) + "s)";
  m[2] = "3. MULAI / SIMPAN";
  
  for(int i=0; i<3; i++){
    if(i == settingMenuIndex) { 
      tft.fillRect(30, 80+(i*40), 260, 35, TFT_WHITE); 
      tft.setTextColor(TFT_BLACK, TFT_WHITE); 
    } else { 
      tft.drawRect(30, 80+(i*40), 260, 35, TFT_WHITE); 
      tft.setTextColor(TFT_WHITE, TFT_BLACK); 
    }
    tft.drawString(m[i], 40, 85+(i*40), 2);
  }
  tft.setTextColor(TFT_LIGHTGREY, TFT_BLACK); 
  tft.drawCentreString("2:UP 8:DOWN #:EDIT", 160, 220, 2);
}

void drawLevelMonitor() {
  tft.fillScreen(TFT_BLACK); 
  tft.setTextColor(TFT_WHITE); 
  tft.drawCentreString("LEVEL TANGKI", 160, 10, 2);
  
  tft.drawRect(40, 40, 60, 150, TFT_WHITE); 
  int h1 = constrain(map(glb_LvlChamber, 0, 100, 0, 150), 0, 150);
  tft.fillRect(41, 40+(150-h1), 58, h1, TFT_BLUE);
  tft.drawCentreString("CHAMBER", 70, 200, 1); 
  tft.drawCentreString(String(glb_LvlChamber)+"%", 70, 100, 2);

  tft.drawRect(200, 40, 60, 150, TFT_WHITE); 
  int h2 = constrain(map(glb_LvlAqua, 0, 100, 0, 150), 0, 150);
  tft.fillRect(201, 40+(150-h2), 58, h2, TFT_CYAN);
  tft.drawCentreString("AQUARIUM", 230, 200, 1); 
  tft.drawCentreString(String(glb_LvlAqua)+"%", 230, 100, 2);
}

void drawSettingScreen(String t, String v, String u, int o) {
  tft.fillScreen(TFT_BLACK); 
  tft.setTextColor(TFT_YELLOW); tft.drawCentreString(t, 160, 30, 4);
  tft.setTextColor(TFT_CYAN); tft.drawCentreString("Saat ini: " + String(o) + " " + u, 160, 70, 2);
  tft.setTextColor(TFT_WHITE); tft.drawRect(60, 100, 200, 40, TFT_WHITE);
  tft.drawCentreString(v, 160, 110, 4);
  tft.setTextColor(TFT_LIGHTGREY); tft.drawCentreString("#:ENTER C:CLEAR", 160, 200, 2);
}

void drawConfirmation() {
  tft.fillScreen(TFT_BLACK); 
  tft.setTextColor(TFT_RED); tft.drawCentreString("KONFIRMASI", 160, 30, 4);
  tft.setTextColor(TFT_WHITE); 
  tft.drawString("Interval : " + String(set_Interval) + " Menit", 40, 80, 2);
  tft.drawString("Durasi   : " + String(set_Durasi) + " Detik", 40, 110, 2);
  tft.setTextColor(TFT_GREEN); tft.drawCentreString("JALANKAN?", 160, 160, 2);
  tft.setTextColor(TFT_LIGHTGREY); tft.drawCentreString("#: YA *: BATAL", 160, 200, 2);
}

void drawChartView() {
  tft.fillScreen(TFT_BLACK); 
  tft.setTextColor(TFT_WHITE); tft.drawString("GRAFIK REALTIME", 10, 5, 2);
  tft.drawFastHLine(0, 85, 320, TFT_GREY); tft.drawFastHLine(0, 145, 320, TFT_GREY);
  
  tft.setTextColor(TFT_CYAN); tft.drawString("pH", 5, 30, 1);
  for(int i=0; i<29; i++){
    int y1 = map((int)(chartPH[i]*10), 0, 140, 80, 30);
    int y2 = map((int)(chartPH[i+1]*10), 0, 140, 80, 30);
    tft.drawLine(20+(i*10), y1, 20+((i+1)*10), y2, TFT_CYAN);
  }
  
  tft.setTextColor(TFT_ORANGE); tft.drawString("Turb", 5, 90, 1);
  for(int i=0; i<29; i++){
    int y1 = map((int)chartTurb[i], 0, 100, 140, 90);
    int y2 = map((int)chartTurb[i+1], 0, 100, 140, 90);
    tft.drawLine(20+(i*10), y1, 20+((i+1)*10), y2, TFT_ORANGE);
  }
  
  tft.setTextColor(TFT_GREEN); tft.drawString("TDS", 5, 150, 1);
  for(int i=0; i<29; i++){
    int y1 = map((int)chartTDS[i], 0, 500, 200, 150);
    int y2 = map((int)chartTDS[i+1], 0, 500, 200, 150);
    tft.drawLine(20+(i*10), y1, 20+((i+1)*10), y2, TFT_GREEN);
  }
}