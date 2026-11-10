# dashboard-charging

Dashboard Monitoring Ampere Charging (Line A & Line F) berbasis **CodeIgniter 4 (CI4)** dengan visualisasi grafik interaktif menggunakan **ApexCharts** dan portal pemilihan lini yang modern.

## 📌 Fitur Utama
- **Portal Pemilihan Lini (Modern Hub UI/UX)**: Halaman portal pilihan interaktif untuk mengakses **Charging Line A** dan **Charging Line F**.
- **Realtime Live Monitoring**: Pemantauan grafik Ampere dan Voltase secara berkala (auto-refresh tiap 3 detik).
- **Fast Line Switcher**: Navigasi cepat berganti antara Line A dan Line F langsung dari dalam dashboard.
- **Historical Analysis**: Filter data berdasarkan rentang tanggal/jam dan resolusi interval (Raw, 1m, 10m, 30m, 1h).
- **Status Charging Pintar**:
  - 🟢 **Active Charging**: Ampere dan Voltase aktif terbaca.
  - 🟡 **Rest Charging**: Voltase terbaca, Ampere nol.
  - 🔴 **Off Charging**: Tidak ada arus atau tegangan terbaca.
- **Circuit Selector**: Navigasi cepat untuk 10 Circuit terupdate (prioritas aktif) serta pilihan lengkap Circuit 1 - 110.
- **Simulasi / Demo Mode**: Mode preview data simulasi saat server offline / tidak terhubung ke LAN SQL Server.

## 🗄️ Sumber Tabel Database SQL Server
- **Charging Line A**: `[prod_control].[dbo].[z_amp_chargingA]`
- **Charging Line F**: `[prod_control].[dbo].[z_amp_chargingF]`

## 🌐 Struktur Halaman & URL
- **Portal Pemilihan**: `/` atau `/portal`
- **Dashboard Line A**: `/charging/a`
- **Dashboard Line F**: `/charging/f`
- **API Realtime/Histori**: `/api/charging?line=A` atau `/api/charging?line=F`

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
   
   database.default.hostname = your_sql_server_ip
   database.default.database = your_database_name
   database.default.username = your_username
   database.default.password = your_password
   database.default.DBDriver = SQLSRV
   database.default.port = 1433
   ```

4. **Jalankan Aplikasi**:
   - Melalui Laragon: Buka `http://localhost/dashboard_charging/` atau `http://dashboard_charging.test/`
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
