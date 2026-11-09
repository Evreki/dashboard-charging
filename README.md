# dashboard-charging

Dashboard Monitoring Ampere Charging A berbasis **CodeIgniter 4 (CI4)** dengan visualisasi grafik interaktif menggunakan **ApexCharts**.

## 📌 Fitur Utama
- **Realtime Live Monitoring**: Pemantauan grafik Ampere dan Voltase secara berkala (auto-refresh tiap 3 detik).
- **Historical Analysis**: Filter data berdasarkan rentang tanggal/jam dan resolusi interval (Raw, 1m, 10m, 30m, 1h).
- **Status Charging Pintar**:
  - 🟢 **Active Charging**: Ampere dan Voltase aktif terbaca.
  - 🟡 **Rest Charging**: Voltase terbaca, Ampere nol.
  - 🔴 **Off Charging**: Tidak ada arus atau tegangan terbaca.
- **Circuit Selector**: Navigasi cepat untuk 10 Circuit terupdate (prioritas aktif) serta pilihan lengkap Circuit 1 - 110.
- **Simulasi / Demo Mode**: Mode preview data simulasi saat server offline / tidak terhubung ke LAN SQL Server.

## 🛠️ Persyaratan Sistem
- PHP >= 8.2 (dengan ekstensi `sqlsrv` atau `pdo_sqlsrv`, `intl`, `mbstring`)
- Composer
- Laragon / Web Server (Apache/Nginx) atau CLI `php spark serve`
- Microsoft SQL Server (`prod_control`)

## 🚀 Panduan Instalasi & Menjalankan

1. **Clone repository**:
   ```bash
   git clone https://github.com/Evreki/dashboard-charging.git
   cd dashboard-charging
   ```

2. **Install dependency composer**:
   ```bash
   composer install
   ```

3. **Konfigurasi Environment**:
   Salin file `env` menjadi `.env` lalu sesuaikan konfigurasi database dan base URL:
   ```ini
   CI_ENVIRONMENT = development
   app.baseURL = 'http://localhost/dashboard_charging/public/'
   
   database.default.hostname = 10.19.16.21
   database.default.database = prod_control
   database.default.username = sql_pre
   database.default.password = User@eng1
   database.default.DBDriver = SQLSRV
   database.default.port = 1433
   ```

4. **Jalankan Aplikasi**:
   - Melalui Laragon: Buka `http://localhost/dashboard_charging/` atau `http://dashboard-charging.test/`
   - Melalui Spark:
     ```bash
     php spark serve
     ```
     Buka di browser: `http://localhost:8080/`

## 🧪 Testing
Jalankan tes verifikasi integrasi:
```bash
php tests/test_charging.php
```
