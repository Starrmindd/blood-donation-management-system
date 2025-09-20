
# 🩸 Blood Donation Management System  

A PHP & MySQL-based web application for managing blood donations, inventory, donation campaigns, alerts, and requests.  

## 🚀 Features  
- User authentication (**Admin, Staff, Donor**)  
- Manage **donors** and their details  
- Track **blood inventory** by blood group  
- Organize **donation campaigns**  
- Issue **alerts/notifications** (emergencies, reminders)  
- Record **donations** and **blood requests**  
- Analytics dashboard for admins  

---

## ⚙️ Installation  

### 1. Requirements  
- [XAMPP](https://www.apachefriends.org/) or any PHP + MySQL server  
- PHP 7.4+ (PDO enabled)  
- MySQL 5.7+  

### 2. Setup  
1. Clone or download the project into your server directory (`htdocs` for XAMPP).  
   ```bash
   C:\xampp\htdocs\blood\
````

2. Create a database in MySQL:

   ```sql
   CREATE DATABASE blood_donation;
   ```

3. Import the SQL schema (with dummy data) into phpMyAdmin or MySQL CLI:

   * Open phpMyAdmin → select `blood_donation` → Import → choose `blood_donation.sql`.

4. Configure your database connection in `db.php`:

   ```php
   <?php
   $host = "localhost";
   $dbname = "blood_donation";
   $username = "root";   // default for XAMPP
   $password = "";       // default for XAMPP

   try {
       $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
       $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   } catch (PDOException $e) {
       die("Database connection failed: " . $e->getMessage());
   }
   ?>
   ```

5. Start Apache & MySQL from XAMPP Control Panel.

6. Open the project in your browser:

   ```
   http://localhost/blood/
   ```

---

## 🗄️ Database Schema

**Tables included:**

* `users` (admins, staff, donors)
* `donors` (donor details)
* `blood_inventory` (stock levels)
* `campaigns` (blood donation drives)
* `alerts` (notifications/emergencies)
* `donations` (records of blood donations)
* `requests` (blood requests from users/hospitals)

---

## 🔑 Default Credentials

```txt
Admin Login:
Email: admin@blood.com
Password: admin123

Staff Login:
Email: staff@hospital.com
Password: password

Donor Login:
Email: john@example.com
Password: password
```

---

## 📊 Dummy Data Included

* 2 donors (John, Jane)
* 5 blood groups in stock (A+, A-, B+, O+, O-)
* 2 campaigns (Blood Drive, Community Outreach)
* 2 alerts (Emergency Needed, Campaign Reminder)
* 2 donations
* 2 requests

---

## 📌 Notes

* If you see **“table/column not found”** errors, it means the database schema does not match your PHP code. Update either the **SQL schema** or the **queries in PHP**.
* Update the `alerts` table column name (`body` vs `message`) depending on your version of the code.

```

---

