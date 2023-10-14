#include <Arduino.h>
#include <HardwareSerial.h>
#include "stdio.h"
#include "string.h"
#include <Adafruit_MLX90614.h>
#include <WiFi.h>
#include "SPI.h"
#include "MFRC522.h"
#include <Preferences.h>
#include <NTPClient.h>
#include <WiFiUdp.h>
#include <RTClib.h>
#include "FS.h"
#include "SD.h"
#include "Nextion.h"
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <Update.h>
#include <esp_smartconfig.h>
/*-----------------------------------------PRIVATE DEFINE------------------------------------------*/
/*-------------------------------------------------------------------------------------------------*/
#define GM65                       Serial1
#define SIM7600                    Serial
#define RX1                        25
#define TX1                        26
#define RX_BUFFER_SIZE             256
//PIN DEFINE
#define CS_PIN                     33
#define RST_PIN                    32
#define LED_NET                    2
#define BATTERY                    39
#define BUZZER                     27
#define SWITCH_MODE_BUTTON         14
#define WAKE_UP_BUTTON             13
//SYSTEM SETUP DEFAULT
#define BUFFER_Nextion             9    //Kích thước bộ đệm dữ liệu nhận +1 (tinh ca \0)
#define SLEEP_TIME_SYSTEM          0
#define SLEEP_ENABLE_SYSTEM        false
//DATA RECEIVE NEXTION
#define LOG_IN                    "Log_In1@"
#define HOME                      "HomeScr@"
#define REGISTRATION_TAG          "NewTag1@"
#define READ_TAG                  "ReadTag@"
#define READ_VATTU                "Mavach1@"
#define ADD_INFOR                 "AddInfo@"
#define SCAN_TAG                  "ReadInf@"
#define UPDATE_NEW_TAG            "Upd_Obj@"
#define UADATE_DATA               "Upd_dat@"
#define SETTING                   "Setting@"
#define SET_TIME_SLEEP_MCU        "SleepMC@"
#define SET_TIME                  "SetTime@"
#define SLEEP_ON                  "Sleepon@"
#define SLEEP_OFF                 "Sleepof@"
#define SET_DEFAULT               "SetDefa@"
#define DEFAULT_MODE              "Default@"
#define CHECK_ERROR               "CheckEr@"
#define LOG_OUT                   "Logout1@"
#define UPLOAD_DATA_OFFLINE       "Upload1@"
#define MA_DINH_DANH              "ScanUID@"
#define MA_VACH                   "Scan_ID@"
#define UPLOAD                    "Adding1@"
#define UPDATE_FIRMWARE           "OTAfile@"
#define WIFI                      "Connect@"
//FILE NAME
#define HISTORY_FILE              "/Log_Data.txt"
#define CHECK_ERROR_FILE          "/Check_Error.txt"
#define RFID                      "/OFFLINE/RFID.txt"
#define VAT_TU                    "/OFFLINE/VATTU.txt"
#define FEDDING_VACCINE           "/OFFLINE/ACTION.txt"
#define DIRNAME                   "/OFFLINE"
//SERVER URL
#define getUID                    "https://doantotnghiep2k.000webhostapp.com/getUID.php"
#define Regis_RFID                "https://doantotnghiep2k.000webhostapp.com/Update_tag.php"
#define Action                    "https://doantotnghiep2k.000webhostapp.com/feeding_vaccine.php"
#define Regis_GM65                "https://doantotnghiep2k.000webhostapp.com/Update_vattu.php"
#define Read_UID                  "https://doantotnghiep2k.000webhostapp.com/send_to_esp32_RFID.php?id="
#define Read_VATTU                "https://doantotnghiep2k.000webhostapp.com/send_to_esp32_GM65.php?id="
#define Login                     "https://doantotnghiep2k.000webhostapp.com/check_login.php?user="
#define Upload                    "https://doantotnghiep2k.000webhostapp.com/firmware/firmware.bin"
#define Read_Tag                  "https://doantotnghiep2k.000webhostapp.com/read_tag_user_data.php?id="
/*-----------------------------------------PRIVATE TYPEDEF-----------------------------------------*/
/*-------------------------------------------------------------------------------------------------*/
MFRC522           mfrc522(CS_PIN, RST_PIN);
Adafruit_MLX90614 mlx = Adafruit_MLX90614();
RTC_DS3231        rtc;
WiFiUDP           ntpUDP;
NTPClient         timeClient(ntpUDP);
Preferences       preferences;

struct Response{
  uint8_t RC522_ERROR;
  uint8_t MLX_ERROR;
  uint8_t SD_ERROR;
  uint8_t SIM_ERROR=1;
};
Response Readsuscess; //BANG 1 KHI DOC THANH CONG, 0 KHI LOI
/*-----------------------------------------PRIVATE VARIBLE-----------------------------------------*/
/*-------------------------------------------------------------------------------------------------*/
RTC_DATA_ATTR char Buf_After[BUFFER_Nextion]={0}, User[20]={0}, Pass[20]={0};
RTC_DATA_ATTR String ID;
/*--------------------------MAN HINH DANG NHAP--------------------------*/
NexPicture      pNet0      = NexPicture(0, 7, "pNet0");           //page 0
NexPicture      pBat0      = NexPicture(0, 8, "pBat0");           //page 0
NexText         tUser      = NexText(0, 4, "tUser");              //page 0
NexText         tPass      = NexText(0, 3, "tPass");              //page 0
NexPage         page0      = NexPage(0, 0, "page0");              //page 0
NexText         t0         = NexText(0, 5, "t0");                 //page 0
/*--------------------------MAN HINH CHUC NANG--------------------------*/
NexPicture      pNet1      = NexPicture(1, 10, "pNet1");          //page 1
NexPicture      pBat1      = NexPicture(1, 11, "pBat1");          //page 1
NexPage         page1      = NexPage(1, 0, "page1");              //page 1
NexText         t1         = NexText(1, 9, "t1");                 //page 1
/*-----------------------MAN HINH DOC MA THE RFID-----------------------*/
NexPicture      pNet2      = NexPicture(2, 13, "pNet2");          //page 2
NexPicture      pBat2      = NexPicture(2, 14, "pBat2");          //page 2
NexText         tUID       = NexText(2, 2, "tUID");               //page 2
NexText         tTemp      = NexText(2, 4, "tTemp");              //page 2
NexText         tGender    = NexText(2, 5, "tGender");            //page 2
NexText         tAge       = NexText(2, 6, "tAge");               //page 2
NexText         tVaccine   = NexText(2, 7, "tVaccine");           //page 2
NexText         tFood      = NexText(2, 9, "tFood");              //page 2
NexText         tSpecies   = NexText(2, 11, "tSpecies");          //page 2
NexText         tHistory   = NexText(2, 8, "tHistory");           //page 2
/*----------------------MAN HINH THEM THE RFID MOI----------------------*/
NexPicture      pNet3      = NexPicture(3, 11, "pNet3");          //page 3
NexPicture      pBat3      = NexPicture(3, 12, "pBat3");          //page 3
NexText         TUID       = NexText(3, 3, "TUID");               //page 3
NexText         TTemp      = NexText(3, 8, "TTemp");              //page 3 
NexText         TGender    = NexText(3, 7, "TGender");            //page 3
NexText         TSpecies   = NexText(3, 6, "TSpecies");           //page 3
NexText         TBirthday  = NexText(3, 5, "TBirthday");          //page 3
NexText         THistory   = NexText(3, 9, "THistory");           //page 3 
/*-----------------------MAN HINH THEM VAT TU MOI-----------------------*/
NexPage         page4      = NexPage(4, 0, "page4");              //page 4
NexPicture      pNet4      = NexPicture(4, 8, "pNet4");           //page 4
NexPicture      pBat4      = NexPicture(4, 9, "pBat4");           //page 4
NexText         tID        = NexText(4, 2, "tID");                //page 4
NexText         tName      = NexText(4, 3, "tName");              //page 4
NexText         tClass     = NexText(4, 6, "tClass");             //page 4
NexText         tHsd       = NexText(4, 10, "tHsd");              //page 4
/*------------------------MAN HINH CAI DAT CHUNG------------------------*/
NexPicture      pNet5      = NexPicture(5, 15, "pNet5");          //page 5
NexPicture      pBat5      = NexPicture(5, 16, "pBat5");          //page 5
NexNumber       nSleep     = NexNumber(5, 6, "nSleep");           //page 5
NexText         tTime      = NexText(5, 7, "tTime");              //page 5
NexDSButton     bSleepOn   = NexDSButton(5, 13, "bSleepOn");      //page 5
/*----------------MAN HINH KIEM TRA LOI THIET BI CAM TAY----------------*/
NexCheckbox     cRC522     = NexCheckbox(6, 2, "cRC522");         //page 6
NexCheckbox     cMLX       = NexCheckbox(6, 3, "cMLX");           //page 6
NexCheckbox     cSD        = NexCheckbox(6, 8, "cSD");            //page 6
NexCheckbox     cSim       = NexCheckbox(6, 9, "cSim");           //page 6
NexScrolltext   gErrorText = NexScrolltext(6, 12, "gErrorText");  //page 6
/*---------------MAN HINh GHI LICH SU CHO AN VA TIEM PHONG--------------*/
NexPicture      pNet9      = NexPicture(9, 3, "pNet9");           //page 9
NexPicture      pBat9      = NexPicture(9, 4, "pBat9");           //page 9
NexText         xUID        = NexText(9, 5, "xUID");              //page 9
NexText         xID         = NexText(9, 6, "xID");               //page 9
/*---------------------MAN HINH DOC THONG TIN VAT TU--------------------*/
NexPicture      pNet10     = NexPicture(10, 3, "pNet10");         //page 10
NexPicture      pBat10     = NexPicture(10, 4, "pBat10");         //page 10
NexText         gID        = NexText(10, 5, "gID");               //page 10
NexText         gName      = NexText(10, 6, "gName");             //page 10
NexText         gClass     = NexText(10, 7, "gClass");            //page 10
NexText         gHsd       = NexText(10, 8, "gHsd");              //page 10
NexText         t10        = NexText(10, 2, "t10");               //page 10
/*-------------------------------------------USER CODE 0-------------------------------------------*/
/*-------------------------------------------------------------------------------------------------*/
void Buzzer(void);
void Upload_Data();
void GetTime(String* pTime);
void Sleep(void);
//void IRAM_ATTR buttonInterrupt(void);
//void IRAM_ATTR uart0_ISR(void);
void Connect_GPRS(void);
void Nextion_event_handle();
void SendData(const char* serverUrl, const char* postData);
void array_to_string(byte array[], unsigned int len, char buffer[]);
void appendFile(fs::FS &fs, const char * path, const char * message);
void updateFirmware();
uint8_t CheckError(fs::FS &fs, const char * path, const char * message, char* pError_Text);
uint8_t GetTemp_MLX(float* pTemp);
uint8_t GetID(String* pstrUID);
uint32_t Check_Battery();
uint32_t Check_Connect();
/*------------------------------------------INTERRUPT CODE-----------------------------------------*/
/*-------------------------------------------------------------------------------------------------*/
void buttonInterrupt(){
  bool sleepEnable = preferences.getBool("sleepEnable", SLEEP_ENABLE_SYSTEM);
  sleepEnable = !sleepEnable;
  preferences.putBool("sleepEnable", sleepEnable);
}

void Sleep(void){
  bool sleepEnable = preferences.getBool("sleepEnable", SLEEP_ENABLE_SYSTEM);
  if(sleepEnable){
    uint32_t Sleep_MCU = preferences.getUInt("Sleep_MCU", SLEEP_TIME_SYSTEM);
    digitalWrite(LED_NET, LOW);
    delay(100);
    esp_sleep_enable_ext0_wakeup(GPIO_NUM_13, LOW);
    if(Sleep_MCU > 0){
      esp_sleep_enable_timer_wakeup(Sleep_MCU*1000000ULL);
    }
    esp_deep_sleep_start();
  }
}
/*-------------------------------------------SET UP CODE-------------------------------------------*/
/*-------------------------------------------------------------------------------------------------*/
void setup() {
  SIM7600.begin(9600);
  nexSerial.begin(9600);
  GM65.begin(9600, SERIAL_8N1, RX1, TX1);
  SPI.begin();
  pinMode(LED_NET, OUTPUT);
  pinMode(BUZZER, OUTPUT);
  pinMode(SWITCH_MODE_BUTTON, INPUT_PULLUP);
  pinMode(WAKE_UP_BUTTON, INPUT_PULLUP);
  analogReadResolution(12);        // Độ phân giải 12 bit
  analogSetAttenuation(ADC_11db);  // Đặt ngưỡng đầu vào để đo 0-3.3V
  nexInit();
  preferences.begin("my-app", false);
  mfrc522.PCD_Init();
  SD.begin();
  mlx.begin();
  rtc.begin();
  Connect_GPRS();
  attachInterrupt(digitalPinToInterrupt(SWITCH_MODE_BUTTON), buttonInterrupt, FALLING);

  esp_reset_reason_t reason = esp_reset_reason();
  if (reason == ESP_RST_POWERON) {
    page0.show();
    t0.setText("System successfully");
    pBat0.setPic(Check_Battery());
    pNet0.setPic(Check_Connect());
  }
  nexSerial.print("hihi");
  nexSerial.write(0xFF);
  nexSerial.write(0xFF);
  nexSerial.write(0xFF);
}
/*--------------------------------------------LOOP CODE--------------------------------------------*/
/*-------------------------------------------------------------------------------------------------*/
void loop() {
  if (nexSerial.available()){
    String Buf_Nex = nexSerial.readStringUntil('@');
    Buf_Nex.trim();
    String substring = Buf_Nex.substring(Buf_Nex.length()-(BUFFER_Nextion-2));
    substring += '@';
    substring.toCharArray(Buf_After, BUFFER_Nextion);
    Serial.println(Buf_After);
  }
  if (GM65.available()) {
    ID = GM65.readStringUntil('@');
    ID.trim();
  }
  Nextion_event_handle();
  ID="";
}
/*-------------------------------------------USER CODE 1-------------------------------------------*/
/*-------------------------------------------------------------------------------------------------*/
void Nextion_event_handle(){
  String dataMessage, dataPost, StrUID, Time;
  float Temp_obj;
  char Error_Text[50];
  if(strcmp(Buf_After, LOG_IN)==0){
    pBat0.setPic(Check_Battery());
    pNet0.setPic(Check_Connect());
    tUser.getText(User, sizeof(User));
    tPass.getText(Pass, sizeof(Pass));
    if (WiFi.status() == WL_CONNECTED){
      HTTPClient http;
      String serverUrl = Login + String(User);
      http.begin(serverUrl);
      int httpCode = http.GET();
      String payload = http.getString();
      DynamicJsonDocument doc(1024);
      deserializeJson(doc, payload);
      String password = doc["password"];
      if(strcmp(password.c_str(), Pass)==0){
        page1.show();
        pNet1.setPic(Check_Connect());
        pBat1.setPic(Check_Battery());
        t1.setText("Login successfully");
      }else{
        t0.setText("User or Password incorrect");
      }
      http.end();
    }else{
      page1.show();
      t1.setText("Offline mode");
      pNet1.setPic(Check_Connect());
      pBat1.setPic(Check_Battery());
      memset(User, 0, sizeof(User));
      memset(Pass, 0, sizeof(Pass));
    }
    GetTime(&Time);
    dataMessage = "["+Time+"]"+"  "+User+" "+ "LOG_IN";
    appendFile(SD, HISTORY_FILE, dataMessage.c_str());
    memset(Buf_After, 0, sizeof(Buf_After));
  }else if(strcmp(Buf_After, WIFI)==0){
    WiFi.beginSmartConfig();
    delay(500);
    while (!WiFi.smartConfigDone()) {
      t0.setText("Start connecting...");
    }
    WiFi.stopSmartConfig();
    String ssid = WiFi.SSID();
    String password = WiFi.psk();
    preferences.putString("ssid", ssid);
    preferences.putString("password", password);
    t0.setText("Connected");
    pNet0.setPic(Check_Connect());
    pBat0.setPic(Check_Battery());
    memset(Buf_After, 0, sizeof(Buf_After));
  }else if(strcmp(Buf_After, HOME)==0){
    page1.show();
    pNet1.setPic(Check_Connect());
    pBat1.setPic(Check_Battery());
    memset(Buf_After, 0, sizeof(Buf_After));
  }else if(strcmp(Buf_After, READ_TAG)==0){
    if(WiFi.status() == WL_CONNECTED){
      if(GetID(&StrUID)==1){
        HTTPClient http;
        String serverUrl = Read_UID + StrUID;
        char temp[10];
        Buzzer();
        tUID.setText(StrUID.c_str());
        GetTemp_MLX(&Temp_obj);
        sprintf(temp, "%.2f ℃", Temp_obj);
        tTemp.setText(temp);
        http.begin(serverUrl);
        int httpCode = http.GET();
        pNet2.setPic(Check_Connect());
        pBat2.setPic(Check_Battery());
        if (httpCode == HTTP_CODE_OK) {
          String payload = http.getString();
          DynamicJsonDocument doc(1024);
          deserializeJson(doc, payload);

          String gender = doc["gender"];
          String species = doc["species"];
          String vaccine = doc["vaccine"];
          String food = doc["food"];
          String history = doc["history"];
          String age = doc["age"];
          age += " tháng";
          vaccine += " mũi";
          tGender.setText(gender.c_str());
          tAge.setText(age.c_str());
          tVaccine.setText(vaccine.c_str());
          tFood.setText(food.c_str());
          tSpecies.setText(species.c_str());
          tHistory.setText(history.c_str());
        }else{
          Buzzer();
          delay(100);
          Buzzer();
        }
        http.end();
        GetTime(&Time);
        dataMessage="["+Time+"]"+"  "+User+" "+"READ_TAG"+" "+StrUID;
        appendFile(SD, HISTORY_FILE, dataMessage.c_str());
      }
      Sleep();
    }else{
      page1.show();
      pNet1.setPic(Check_Connect());
      pBat1.setPic(Check_Battery());
      t1.setText("Lost connect to Server");
      memset(Buf_After, 0, sizeof(Buf_After));
    }
  }else if(strcmp(Buf_After, REGISTRATION_TAG)==0){
    pNet3.setPic(Check_Connect());
    pBat3.setPic(Check_Battery());
    if(GetID(&StrUID)==1){
      char temp[10];
      Buzzer();
      GetTemp_MLX(&Temp_obj);
      sprintf(temp, "%.2f℃", Temp_obj);
      TTemp.setText(temp);
      TUID.setText(StrUID.c_str());
      memset(Buf_After, 0, sizeof(Buf_After));
    }
  }else if(strcmp(Buf_After, UPDATE_NEW_TAG)==0){
    HTTPClient http;
    char tuid[20]={0}, tbirthday[50]={0}, tgender[20]={0}, tspecies[50]={0}, thistory[50]={0};
    GetTime(&Time);
    TUID.getText(tuid, sizeof(tuid));
    TBirthday.getText(tbirthday, sizeof(tbirthday));
    TGender.getText(tgender, sizeof(tgender));
    TSpecies.getText(tspecies, sizeof(tspecies));
    THistory.getText(thistory, sizeof(thistory));
    dataPost = "id="+String(tuid)+"&gender="+tgender+"&species="+tspecies+"&birthday="+tbirthday+"&history="+thistory;
    if (WiFi.status() == WL_CONNECTED){
      SendData(Regis_RFID, dataPost.c_str());
    }else{
      appendFile(SD, RFID, dataPost.c_str());
    }
    dataMessage = "["+Time+"]"+"  "+User+" "+"REGISTRATION_TAG"+" "+tuid;
    appendFile(SD, HISTORY_FILE, dataMessage.c_str());
    strcpy(Buf_After, REGISTRATION_TAG);
  }else if(strcmp(Buf_After, ADD_INFOR)==0){
    pNet4.setPic(Check_Connect());
    pBat4.setPic(Check_Battery());
    if(ID!=""){
      tID.setText(ID.c_str());
      memset(Buf_After, 0, sizeof(Buf_After));
    }
  }else if(strcmp(Buf_After, UADATE_DATA)==0){
    GetTime(&Time);
    char tid[50]={0}, tname[20]={0}, tclass[15]={0}, thsd[15]={0}; 
    tID.getText(tid, sizeof(tid));
    tName.getText(tname, sizeof(tname));
    tClass.getText(tclass, sizeof(tclass));
    tHsd.getText(thsd, sizeof(thsd));

    dataPost = "id="+String(tid)+"&name="+String(tname)+"&class="+String(tclass)+"&hsd="+String(thsd);

    if (WiFi.status() == WL_CONNECTED){
      SendData(Regis_GM65, dataPost.c_str());
    }else{
      appendFile(SD, VAT_TU, dataPost.c_str());
    }
    dataMessage = "["+Time+"]"+"  "+User+" "+"UADATE_DATA"+tid;
    appendFile(SD, HISTORY_FILE, dataMessage.c_str());
    strcpy(Buf_After, ADD_INFOR);
  }else if(strcmp(Buf_After, READ_VATTU)==0){
    if(WiFi.status() == WL_CONNECTED){
      if (ID!=""){
        HTTPClient http;
        String serverUrl = Read_VATTU + ID;
        gID.setText(ID.c_str());
        http.begin(serverUrl);
        int httpCode = http.GET();
        pNet10.setPic(Check_Connect());
        pBat10.setPic(Check_Battery());
        if (httpCode == HTTP_CODE_OK) {
          String payload = http.getString();
          DynamicJsonDocument doc(1024);
          deserializeJson(doc, payload);

          String Name = doc["Name"];
          String Class = doc["Class"];
          String Hsd = doc["Hsd"];
          gName.setText(Name.c_str());
          gClass.setText(Class.c_str());
          gHsd.setText(Hsd.c_str());
        }else{
          Buzzer();
          delay(100);
          Buzzer();
        }
        http.end();
        GetTime(&Time);
        dataMessage = "["+Time+"]"+"  "+User+" "+ "Read_VATTU" +" "+ID;
        appendFile(SD, HISTORY_FILE, dataMessage.c_str());
      }
      Sleep();
    }else{
      t10.setText("Lost connect to Server");
      memset(Buf_After, 0, sizeof(Buf_After));
    }
  }else if(strcmp(Buf_After, SCAN_TAG)==0){
    if (WiFi.status() == WL_CONNECTED){
      if(GetID(&StrUID)==1){
        Buzzer();
        dataPost = "UIDresult=" + StrUID;
        SendData(getUID, dataPost.c_str());
        GetTime(&Time);
        dataMessage = "["+Time+"]"+"  "+User+" "+ "SCAN_TAG" +" "+StrUID;
        appendFile(SD, HISTORY_FILE, dataMessage.c_str());
      }
      Sleep();
    }else{
      page1.show();
      pNet1.setPic(Check_Connect());
      pBat1.setPic(Check_Battery());
      t1.setText("Lost connect to Server");
      memset(Buf_After, 0, sizeof(Buf_After));
    }
  }else if(strcmp(Buf_After, SETTING)==0){
    pNet5.setPic(Check_Connect());
    pBat5.setPic(Check_Battery());
    bool sleepEnable = preferences.getBool("sleepEnable", SLEEP_ENABLE_SYSTEM);
    uint32_t Sleep_MCU = preferences.getUInt("Sleep_MCU", SLEEP_TIME_SYSTEM);
    if (sleepEnable){
      bSleepOn.setValue(1);
    }else{
      bSleepOn.setValue(0);
    }
    nSleep.setValue(Sleep_MCU);
    memset(Buf_After, 0, sizeof(Buf_After));
  }else if(strcmp(Buf_After, SET_TIME_SLEEP_MCU)==0){
    GetTime(&Time);
    uint32_t Sleep_MCU;
    nSleep.getValue(&Sleep_MCU);
    preferences.putUInt("Sleep_MCU", Sleep_MCU);
    preferences.putBool("sleepEnable", true);
    strcpy(Buf_After, SETTING);
    dataMessage = "["+Time+"]"+"  "+User+" "+"SETTING_SYSTEM";
    appendFile(SD, HISTORY_FILE, dataMessage.c_str());
  }else if(strcmp(Buf_After, SET_TIME)==0){
    char Time_set[20];
    tTime.getText(Time_set, sizeof(Time_set));
    int year, month, day, hour, minute, second;
    sscanf(Time_set, "%d-%d-%d-%d-%d-%d", &year, &month, &day, &hour, &minute, &second);;
    DateTime dateTime(year, month, day, hour, minute, second);
    rtc.adjust(dateTime);
    strcpy(Buf_After, SETTING);
    GetTime(&Time);
    dataMessage = "["+Time+"]"+"  "+User+" "+"SETTING_SYSTEM";
    appendFile(SD, HISTORY_FILE, dataMessage.c_str());
  }else if(strcmp(Buf_After, SET_DEFAULT)==0){
    uint32_t Sleep_MCU = preferences.getUInt("Sleep_MCU", SLEEP_TIME_SYSTEM);
    preferences.putUInt("Sleep_default", Sleep_MCU);
    strcpy(Buf_After, SETTING);
  }else if(strcmp(Buf_After, DEFAULT_MODE)==0){
    uint32_t Sleep_MCU = preferences.getUInt("Sleep_default", SLEEP_TIME_SYSTEM);
    preferences.putUInt("Sleep_MCU", Sleep_MCU);
    nSleep.setValue(Sleep_MCU);
    strcpy(Buf_After, SETTING);
  }else if(strcmp(Buf_After, SLEEP_ON)==0){
    preferences.putBool("sleepEnable", true);
    strcpy(Buf_After, SETTING);
  }else if(strcmp(Buf_After, SLEEP_OFF)==0){
    preferences.putBool("sleepEnable", false);
    strcpy(Buf_After, SETTING);
  }else if(strcmp(Buf_After, CHECK_ERROR)==0){
    if(GetID(&StrUID)==1){
      GetTemp_MLX(&Temp_obj);
      GetTime(&Time);
      cRC522.setValue(Readsuscess.RC522_ERROR);
      cMLX.setValue(Readsuscess.MLX_ERROR);
      cSim.setValue(Readsuscess.SIM_ERROR);
      dataMessage = "["+Time+"]"+"  "+User+" "+ "CHECK_ERROR"+"\n";
      CheckError(SD, CHECK_ERROR_FILE, dataMessage.c_str(), Error_Text);
      cSD.setValue(Readsuscess.SD_ERROR);
      dataMessage = "["+Time+"]"+"  "+User+" "+"Successfully RFID = "   + String(Readsuscess.RC522_ERROR) + ", "
                                              + "MLX = "                + String(Readsuscess.MLX_ERROR) + ", "
                                              + "SIMA7600 = "           + String(Readsuscess.SIM_ERROR) + ", "
                                              + "SDCard = "             + String(Readsuscess.SD_ERROR);
      CheckError(SD, CHECK_ERROR_FILE, dataMessage.c_str(), Error_Text);
      gErrorText.setText(Error_Text);
      memset(Buf_After, 0, sizeof(Buf_After));
    }
  }else if(strcmp(Buf_After, LOG_OUT)==0){
    memset(User, 0, sizeof(User));
    memset(Pass, 0, sizeof(Pass));
    page0.show();
    tUser.setText(User);
    tPass.setText(Pass);
    GetTime(&Time);
    dataMessage = "["+Time+"]"+"  "+User+" "+ "LOG_OUT";
    appendFile(SD, HISTORY_FILE, dataMessage.c_str());
    memset(Buf_After, 0, sizeof(Buf_After));
  }else if(strcmp(Buf_After, MA_DINH_DANH)==0){
    char uid[20]={0};
    pNet9.setPic(Check_Connect());
    pBat9.setPic(Check_Battery());
    if(GetID(&StrUID)==1){
      Buzzer();
      xUID.setText(StrUID.c_str());
      memset(Buf_After, 0, sizeof(Buf_After));
    }
  }else if(strcmp(Buf_After, MA_VACH)==0){
    char Id[30]={0};
    pNet9.setPic(Check_Connect());
    pBat9.setPic(Check_Battery());
    if(ID!=""){
      xID.setText(ID.c_str());
      memset(Buf_After, 0, sizeof(Buf_After));
    }
  }else if(strcmp(Buf_After, UPLOAD)==0){
    char tuid[20] = {0}, tid[30]= {0};
    xUID.getText(tuid, sizeof(tuid));
    xID.getText(tid, sizeof(tid));
    dataPost = "UID="+String(tuid)+"&ID="+tid;
    if (WiFi.status() == WL_CONNECTED){
      SendData(Action, dataPost.c_str());
    }else{
      appendFile(SD, FEDDING_VACCINE, dataPost.c_str());
    }
    xUID.setText("UID");
    xID.setText("Barcode");
    dataMessage = "["+Time+"]"+"  "+User+" "+"UPLOAD_VAT_TU"+" "+tid+tuid;
    GetTime(&Time);
    appendFile(SD, HISTORY_FILE, dataMessage.c_str());
    memset(Buf_After, 0, sizeof(Buf_After));
  }else if(strcmp(Buf_After, UPDATE_FIRMWARE)==0){
    if (WiFi.status() == WL_CONNECTED){
      updateFirmware();
    }else{
      t1.setText("Lost connect to Server");
    }
    dataMessage = "["+Time+"]"+"  "+User+" "+"UPDATE_FIRMWARE";
    GetTime(&Time);
    appendFile(SD, HISTORY_FILE, dataMessage.c_str());
    memset(Buf_After, 0, sizeof(Buf_After));
  }else if(strcmp(Buf_After, UPLOAD_DATA_OFFLINE)==0){
    if (WiFi.status() == WL_CONNECTED){
      Upload_Data();
    }else{
      t1.setText("Lost connect to Server");
    }
    memset(Buf_After, 0, sizeof(Buf_After));
  }
}

uint8_t CheckError(fs::FS &fs, const char* path, const char* message, char* pError_Text){
  if(SD.cardType() == CARD_NONE){
    strcpy(pError_Text, "Not Available Card");
    return Readsuscess.SD_ERROR = 0;
  }
  File file = fs.open(path, FILE_APPEND);
  if(!file){
    strcpy(pError_Text, "Failed to open file for appending");
    return Readsuscess.SD_ERROR = 0;
  }
  if(!file.print(message)){
    strcpy(pError_Text, "Failed to write into file");
    return Readsuscess.SD_ERROR = 0;
  }
  Readsuscess.SD_ERROR = 1;
  if (Readsuscess.RC522_ERROR==1 && Readsuscess.MLX_ERROR==1 && Readsuscess.SD_ERROR==1 && Readsuscess.SIM_ERROR==1){
    strcpy(pError_Text, "Read Success");
  }
  file.close();
  return Readsuscess.SD_ERROR;
}

void appendFile(fs::FS &fs, const char * path, const char * message){
    File file = fs.open(path, FILE_APPEND);
    file.println(message);
    file.close();
}

void array_to_string(byte array[], unsigned int len, char buffer[]) {
  for (unsigned int i = 0; i < len; i++)
  {
      byte nib1 = (array[i] >> 4) & 0x0F;
      byte nib2 = (array[i] >> 0) & 0x0F;
      buffer[i*2+0] = nib1  < 0xA ? '0' + nib1  : 'A' + nib1  - 0xA;
      buffer[i*2+1] = nib2  < 0xA ? '0' + nib2  : 'A' + nib2  - 0xA;
  }
  buffer[len*2] = '\0';
}

uint8_t GetID(String* pstrUID) {
  char str[32];
  byte readcard[4];
  if(!mfrc522.PICC_IsNewCardPresent()) {
    return Readsuscess.RC522_ERROR = 0;
  }
  if(!mfrc522.PICC_ReadCardSerial()) {
    return Readsuscess.RC522_ERROR = 0;
  }
  for(int i=0;i<4;i++){
    readcard[i]=mfrc522.uid.uidByte[i];
    array_to_string(readcard, 4, str);
    *pstrUID = str;
  }
  mfrc522.PICC_HaltA();
  return Readsuscess.RC522_ERROR = 1;
}

uint8_t GetTemp_MLX(float* pTemp){
  *pTemp = mlx.readObjectTempC();
  if (isnan(*pTemp)){
    return Readsuscess.MLX_ERROR =0;
  }
  return Readsuscess.MLX_ERROR =1;
}

void GetTime(String* pTime){
  DateTime now = rtc.now();
  char buf2[] = "YYYY/MM/DD hh:mm:ss";
  *pTime = now.toString(buf2);
}

void Buzzer(void){
  digitalWrite(BUZZER, HIGH);
  delay(200);
  digitalWrite(BUZZER, LOW);
}

void Connect_GPRS(void){
  String ssid = preferences.getString("ssid", "");
  String password = preferences.getString("password", "");
  WiFi.begin(ssid.c_str(), password.c_str());
  //WiFi.begin("Tang 1", "keongot123");
  delay(2000);
  if (WiFi.status() != WL_CONNECTED){
    digitalWrite(LED_NET, LOW);
  }else{
    digitalWrite(LED_NET, HIGH);
    timeClient.begin();
    timeClient.setTimeOffset(+7*60*60);   //cài đặt múi giờ VN
    timeClient.update();
    String formattedDate = timeClient.getFormattedDate();
    DateTime dateTime = DateTime(formattedDate.substring(0, 4).toInt(),
                                formattedDate.substring(5, 7).toInt(),
                                formattedDate.substring(8, 10).toInt(),
                                formattedDate.substring(11, 13).toInt(),
                                formattedDate.substring(14, 16).toInt(),
                                formattedDate.substring(17, 19).toInt());
    rtc.adjust(dateTime);
  }
}

void SendData(const char* serverUrl, const char* postData){
  HTTPClient http;
  http.begin(serverUrl);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  int httpCode = http.POST(postData);
  if (httpCode != HTTP_CODE_OK) {
    Buzzer();
    delay(100);
    Buzzer();
  }else{
    Buzzer();
  }
  http.end();
}

uint32_t Check_Battery(){
  uint16_t adcValue = analogRead(BATTERY);
  if(adcValue <= 2.3){
    Buzzer();
    delay(100);
    Buzzer();
    delay(100);
    Buzzer();
    return 5;
  }else{
    return 4;
  }
}

uint32_t Check_Connect(){
  if (WiFi.status() == WL_CONNECTED) {
    digitalWrite(LED_NET, HIGH);
    return 2;
  }else{
    digitalWrite(LED_NET, LOW);
    return 3;
  }
}

void Upload_Data(){
  String postData;
  File root = SD.open(DIRNAME);
  char ch;
  File file = root.openNextFile();
  while(file){
    String File_name=file.name();
    if (File_name=="RFID.txt"){
      t1.setText("Uploading New Tag");
      while (file.available()) {
        ch = file.read();
        if (ch != '\n') {
          postData += ch;
        }else{
          SendData(Regis_RFID, postData.c_str());
          postData="";
        }
      }
      Buzzer();
      SD.remove(RFID);
    }else if(File_name=="VATTU.txt"){
      t1.setText("Uploading data");
      while (file.available()) {
        ch = file.read();
        if (ch != '\n') {
          postData += ch;
        }else{
          SendData(Regis_GM65, postData.c_str());
          postData="";
        }
      }
      Buzzer();
      SD.remove(VAT_TU);
    }else if(File_name=="ACTION.txt"){
      t1.setText("Uploading history");
      while (file.available()) {
        ch = file.read();
        if (ch != '\n') {
          postData += ch;
        }else{
          SendData(Action, postData.c_str());
          postData="";
        }
      }
      Buzzer();
      SD.remove(FEDDING_VACCINE);
    }
    file = root.openNextFile();
  }
  if(!file){
    t1.setText("Upload complete");
  }
  file.close();
  root.close();
}

void updateFirmware() {
  HTTPClient http;
  http.begin(Upload);
  char upload[50]={0};
  int httpCode = http.GET();
  if (httpCode == HTTP_CODE_OK) {
    int contentLength = http.getSize();
    Update.begin(contentLength);
    WiFiClient *stream = http.getStreamPtr();
    uint8_t buf[1024];
    int len = 0;
    int written = 0;
    while (http.connected() && (len = stream->readBytes(buf, sizeof(buf))) > 0) {
      Update.write(buf, len);
      written += len;
      sprintf(upload, "Progress: %d%%\n", (written * 100) / contentLength);
      t1.setText(upload);
    }
    if (Update.end()) {
      Buzzer();
      ESP.restart();
    } else {
      t1.setText("Error upload");
      Buzzer();
      delay(100);
      Buzzer();
    }
  } else {
    t1.setText("Error upload");
    Buzzer();
    delay(100);
    Buzzer();
  }
  http.end();
}