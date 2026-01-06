// =================================================================
// PROGRAM MONITORING AIR - SLAVE MODE (COMMAND BASED)
// =================================================================

#include <Arduino.h>

// --- PIN DEFINITION ---
#define SelenoidChamber 6
#define SelenoidOut     7
#define SelenoidIn      9
#define PompaOutChamber 10
#define PompaIn         11
#define Dinamo          12

#define TRIG1 2 // Aquarium
#define ECHO1 3
#define TRIG2 4 // Chamber
#define ECHO2 5

#define TDS_PIN        A0
#define TURBIDITY_PIN  A1
#define PH_PIN         A2

#define VREF     5.0
#define ADC_RES  1023.0


const float TINGGI_INSTALL_CHAMBER = 12.5; 
const float TINGGI_MAX_AIR_CHAMBER = 12.5;  


const float TINGGI_INSTALL_AQUA = 25.0;    
const float TINGGI_MAX_AIR_AQUA = 25.0;    


float V_PH4 = 4.336; float V_PH6 = 4.005; float V_PH9 = 3.695;
float PH4 = 4.01; float PH6 = 6.86; float PH9 = 9.18;
float ph_slope, ph_intercept, PH_OFFSET = -2.94; 

float V_LEMINERAL = 0.2695; float TDS_LEMINERAL = 86.5;
float V_SUMUR = 0.690;      float TDS_SUMUR = 208.0;
float tds_slope, tds_intercept;

float V_AQUA = 2.7; float NTU_AQUA = 0.6;
float V_TEH = 2.9;  float NTU_TEH = 35.0;
float turb_slope, turb_intercept;


unsigned long durasiPengadukan = 60UL * 1000UL; 


unsigned long lastStandbyUpdate = 0;
const unsigned long intervalStandby = 15000; 

bool isMonitoring = false;

// Variable Penyimpan Data Terakhir
float last_PH=0, last_Turb=0, last_TDS=0;

// Prototypes
void jalankanSekuensMonitoring();
void bacaSensorFull(int detik, String statusStep);
void bacaSensorStandby();
float readUltrasonic(int trigPin, int echoPin);
int hitungPersenLevel(float jarakBaca, float tinggiInstall, float tinggiMaxAir);
void hitungKalibrasiPH(); void hitungKalibrasiTDS(); void hitungKalibrasiTurbidity();
void parseCommand(String cmd);

void setup() {
  Serial.begin(115200); 
  analogReference(DEFAULT);

  pinMode(TRIG1, OUTPUT); pinMode(ECHO1, INPUT);
  pinMode(TRIG2, OUTPUT); pinMode(ECHO2, INPUT);
  pinMode(SelenoidChamber, OUTPUT); pinMode(SelenoidOut, OUTPUT);
  pinMode(SelenoidIn, OUTPUT); pinMode(PompaIn, OUTPUT);
  pinMode(PompaOutChamber, OUTPUT); pinMode(Dinamo, OUTPUT);

  digitalWrite(SelenoidChamber, HIGH); digitalWrite(SelenoidOut, HIGH);
  digitalWrite(SelenoidIn, HIGH); digitalWrite(PompaIn, HIGH);
  digitalWrite(PompaOutChamber, HIGH); digitalWrite(Dinamo, HIGH);

  hitungKalibrasiPH(); hitungKalibrasiTDS(); hitungKalibrasiTurbidity();

  Serial.println(F("[LOG] Setup: Mengosongkan Chamber..."));
  float dist = readUltrasonic(TRIG2, ECHO2);
  while (dist < 12.5) { 
    digitalWrite(PompaOutChamber, LOW);
    delay(1000);
    dist = readUltrasonic(TRIG2, ECHO2);
  }
  digitalWrite(PompaOutChamber, HIGH);

  // --- SINYAL KE ESP32 BAHWA ARDUINO SUDAH SIAP ---
  Serial.println(F("Monitoring:ready"));
}

void loop() {
  unsigned long currentMillis = millis();

  // 1. CEK PERINTAH DARI ESP32
  if (Serial.available() > 0) {
    String data = Serial.readStringUntil('\n');
    data.trim();
    if (data.length() > 0) parseCommand(data);
  }

  // 2. JALANKAN SEQUENCE (HANYA JIKA DIPERINTAH)
  if (isMonitoring) {
    jalankanSekuensMonitoring();
    isMonitoring = false; // Setelah selesai, kembali jadi False
    Serial.println(F("[LOG] Selesai. Masuk Mode Standby."));
    // Kirim sinyal ready lagi setelah selesai, opsional, tapi bagus untuk sync
    Serial.println(F("Monitoring:ready"));
  }

  // 3. UPDATE STANDBY (Tiap 15 Detik)
  // Agar LCD ESP32 tidak kosong/error, tetap kirim level air
  if (!isMonitoring && (currentMillis - lastStandbyUpdate >= intervalStandby)) {
    lastStandbyUpdate = currentMillis;
    bacaSensorStandby(); 
  }
}

// --- LOGIKA PARSING BARU ---
// Menerima format: "Monitoring:60"
void parseCommand(String cmd) {
  if (cmd.startsWith("Monitoring:")) {
    // Ambil angka setelah tanda titik dua
    // "Monitoring:" panjangnya 11 karakter. 
    // Jadi angka mulai dari index 11.
    String durasiStr = cmd.substring(11); 
    int durasiSec = durasiStr.toInt();

    if (durasiSec > 0) {
      // Set durasi pengadukan (pakai UL biar aman)
      durasiPengadukan = (unsigned long)durasiSec * 1000UL;
      isMonitoring = true; // Trigger Loop untuk mulai
      
      Serial.print(F("[LOG] Perintah Diterima. Durasi: "));
      Serial.print(durasiSec);
      Serial.println(F(" detik."));
    }
  }
}

void jalankanSekuensMonitoring() {
  // STEP 1: ISI
  Serial.println(F("[STATUS] Mengisi Air..."));
  digitalWrite(SelenoidChamber, LOW); digitalWrite(PompaIn, LOW); delay(12000); 
  digitalWrite(PompaIn, HIGH); digitalWrite(SelenoidChamber, HIGH); delay(1000);

  // STEP 2: ADUK & UKUR
  Serial.println(F("[STATUS] Mengaduk & Mengukur..."));
  digitalWrite(Dinamo, LOW); 
  unsigned long startUkur = millis();
  int detikKe = 1;
  while (millis() - startUkur < durasiPengadukan) {
    bacaSensorFull(detikKe, "MENGUKUR");
    detikKe++; delay(1000);
  }
  digitalWrite(Dinamo, HIGH);
  
  // STEP 3: TENANG
  Serial.println(F("[STATUS] Menenangkan Air..."));
  delay(5000); 
  bacaSensorFull(0, "SELESAI"); 

  // STEP 4: BUANG
  Serial.println(F("[STATUS] Menguras Chamber..."));
  digitalWrite(PompaOutChamber, LOW); delay(12000); 
  digitalWrite(PompaOutChamber, HIGH);
}

void bacaSensorFull(int detik, String statusStep) {
  float distAqua = readUltrasonic(TRIG1, ECHO1);
  float distChamber = readUltrasonic(TRIG2, ECHO2);
  int pctAqua = hitungPersenLevel(distAqua, TINGGI_INSTALL_AQUA, TINGGI_MAX_AIR_AQUA);
  int pctChamber = hitungPersenLevel(distChamber, TINGGI_INSTALL_CHAMBER, TINGGI_MAX_AIR_CHAMBER);

  long sumPH=0; for(int i=0;i<10;i++){sumPH+=analogRead(PH_PIN); delay(2);}
  float ph = ((ph_slope * ((sumPH/10.0)*VREF/ADC_RES)) + ph_intercept) + PH_OFFSET;
  ph = constrain(ph, 0.0, 14.0);

  long sumTurb=0; for(int i=0;i<10;i++){sumTurb+=analogRead(TURBIDITY_PIN); delay(2);}
  float ntu = max((turb_slope * ((sumTurb/10.0)*VREF/ADC_RES)) + turb_intercept, 0.0f);

  long sumTDS=0; for(int i=0;i<10;i++){sumTDS+=analogRead(TDS_PIN); delay(2);}
  float tds = max((tds_slope * ((sumTDS/10.0)*VREF/ADC_RES)) + tds_intercept, 0.0f);

  last_PH = ph; last_Turb = ntu; last_TDS = tds;

  Serial.print(ph, 1); Serial.print(",");
  Serial.print(ntu, 0); Serial.print(",");
  Serial.print(tds, 0); Serial.print(",");
  Serial.print(pctChamber); Serial.print(",");
  Serial.print(pctAqua); Serial.print(",");
  Serial.print(detik); Serial.print(",");
  Serial.println(statusStep);
}

void bacaSensorStandby() {
  float distAqua = readUltrasonic(TRIG1, ECHO1);
  float distChamber = readUltrasonic(TRIG2, ECHO2);
  int pctAqua = hitungPersenLevel(distAqua, TINGGI_INSTALL_AQUA, TINGGI_MAX_AIR_AQUA);
  int pctChamber = hitungPersenLevel(distChamber, TINGGI_INSTALL_CHAMBER, TINGGI_MAX_AIR_CHAMBER);

  Serial.print(last_PH, 1); Serial.print(",");
  Serial.print(last_Turb, 0); Serial.print(",");
  Serial.print(last_TDS, 0); Serial.print(",");
  Serial.print(pctChamber); Serial.print(","); 
  Serial.print(pctAqua); Serial.print(",");    
  Serial.print(0); Serial.print(",");          
  Serial.println("STANDBY");
}

int hitungPersenLevel(float jarakBaca, float tinggiInstall, float tinggiMaxAir) {
  if (jarakBaca > 900) return 0; 
  float tinggiAirCurrent = tinggiInstall - jarakBaca;
  if (tinggiAirCurrent < 0) tinggiAirCurrent = 0;
  int persen = (int)((tinggiAirCurrent / tinggiMaxAir) * 100.0);
  return constrain(persen, 0, 100);
}

float readUltrasonic(int trigPin, int echoPin) {
  digitalWrite(trigPin, LOW); delayMicroseconds(2);
  digitalWrite(trigPin, HIGH); delayMicroseconds(10);
  digitalWrite(trigPin, LOW);
  long duration = pulseIn(echoPin, HIGH, 25000); 
  return (duration == 0) ? 999 : (duration * 0.034 / 2);
}

void hitungKalibrasiPH() { /*Sama*/ float sumV=V_PH4+V_PH6+V_PH9; float sumPH=PH4+PH6+PH9; float sumVV=V_PH4*V_PH4+V_PH6*V_PH6+V_PH9*V_PH9; float sumVPH=V_PH4*PH4+V_PH6*PH6+V_PH9*PH9; ph_slope=(3*sumVPH-sumV*sumPH)/(3*sumVV-sumV*sumV); ph_intercept=(sumPH-ph_slope*sumV)/3.0; }
void hitungKalibrasiTDS() { tds_slope=(TDS_SUMUR-TDS_LEMINERAL)/(V_SUMUR-V_LEMINERAL); tds_intercept=TDS_LEMINERAL-(tds_slope*V_LEMINERAL); }
void hitungKalibrasiTurbidity() { turb_slope=(NTU_TEH-NTU_AQUA)/(V_TEH-V_AQUA); turb_intercept=NTU_AQUA-(turb_slope*V_AQUA); }