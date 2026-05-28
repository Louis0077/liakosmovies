# 🎬 Οι Ταινίες του Λιάκου

Μια web εφαρμογή διαχείρισης ταινιών φτιαγμένη με PHP και MySQL.  
Επιτρέπει την προβολή, προσθήκη, επεξεργασία και διαγραφή ταινιών μέσω ενός admin panel.

---

## Χαρακτηριστικά

- 🔐 Σύνδεση με λογαριασμό admin
- 🎥 Προβολή όλων των ταινιών με εικόνα, είδος και κατάσταση
- 🔍 Αναζήτηση και φιλτράρισμα ανά είδος
- ➕ Προσθήκη νέας ταινίας με upload εικόνας
- ✏️ Επεξεργασία υπάρχουσας ταινίας
- 🗑️ Διαγραφή ταινίας με επιβεβαίωση
- 📄 Σελίδα λεπτομερειών για κάθε ταινία

---

## 🛠️ Τεχνολογίες

- **PHP** — Backend λογική
- **MySQL** — Βάση δεδομένων
- **HTML / CSS** — Frontend
- **InfinityFree** — Free hosting

---

## ⚙️ Εγκατάσταση τοπικά

1. Clone this repository:
```bash
   git clone https://github.com/USERNAME/liakosmovies.git
```

2. Αντέγραψε το αρχείο παραμέτρων βάσης:
```bash
   cp db_connect.example.php db_connect.php
```

3. Άνοιξε το `db_connect.php` και βάλε τα δικά σου credentials:
```php
   $servername = "localhost";
   $username   = "your_username";
   $password   = "your_password";
   $dbname     = "your_database";
```

4. Δημιούργησε τη βάση δεδομένων και εισήγαγε το SQL schema.

5. Άνοιξε τον φάκελο στον local server σου (π.χ. XAMPP/WAMP).

---

## Δομή αρχείων
