# Mini Project

Mini Project adalah aplikasi sederhana di bidang **Finance & Accounting** yang dibangun menggunakan **Laravel 12**.  
Project ini memiliki fitur **migration**, **seeder**, dan **export Excel**, serta mendukung integrasi dengan **RESTful API**.

---

## 🚀 Teknologi yang Digunakan
- [Laravel 12](https://laravel.com/) - Backend framework
- [Bootstrap 3](https://getbootstrap.com/docs/3.4/) - CSS framework
- [Font Awesome 7](https://fontawesome.com/) - Icon library
- [DataTables](https://datatables.net/) - Tabel interaktif
- [AJAX](https://developer.mozilla.org/en-US/docs/Web/Guide/AJAX) - Asynchronous request
- RESTful API - Integrasi layanan
- Recomendation PHP version (8.2.x) - (8.4.x)

---

## 📦 Fitur Utama
- **Manajemen Data Finance & Accounting**
- **Migration & Seeder** untuk struktur dan data awal
- **Export Excel** untuk laporan
- **Integrasi RESTful API**
- **Tabel Interaktif** menggunakan DataTables

---

## ⚙️ Instalasi & Menjalankan Project

1. Clone repository:
   ```bash
   git clone https://github.com/CahyaSyn/Mini-Project.git
3. Configurasi .env
   ```bash
   - Rename .env.example ke .env
   - Ganti database bisa menggunakan sqlite/mysql
5. Generate key
   ```bash
   composer update
   php artisan key:generate
7. Migration & seed DB
   ```bash
   php artisan migrate:fresh --seed
8. Jalankan program
   ```bash
   composer run dev
atau menggunakan 2 tab terminal bersamaan
   ```bash
   npm run dev
   php artisan serve
