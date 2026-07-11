# ☕ Secangkir Coffee: SQL-Powered Inventory & Sales Management System

![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/mysql-%2300f.svg?style=for-the-badge&logo=mysql&logoColor=white)
![XAMPP](https://img.shields.io/badge/Xampp-F37623?style=for-the-badge&logo=xampp&logoColor=white)

The Secangkir Coffee Inventory System is a fully integrated, web-based solution designed to streamline operations for a rapidly expanding artisanal café in Parit Raja, Johor. The application manages complex tracking for over 150 inventory items, including premium coffee beans and bakery ingredients, to serve over 100 daily customers.

---

## 🚀 The Problem Solved

Currently, Secangkir Coffee relies entirely on manual processes for tracking inventory, utilizing paper checklists that are later transferred to Google Sheets. Sales are recorded using a basic cash register and handwritten receipts, which can lead to a 5-7% discrepancy in end-of-day cash. Furthermore, supplier orders are coordinated via WhatsApp, leading to disorganized record-keeping.

**Secangkir Coffee Inventory System** solves this by providing a centralized MySQL database and custom PHP backend to unify operations. It eliminates scattered paper-based records, reduces the risk of data loss, and actively prevents stockouts or overstocking through an automated tracking ecosystem.

---

## ✨ Key Features

* 📦 **Inventory Tracking Module:** The system provides real-time stock monitoring and updates. It enables authorized staff to add, edit, or remove ingredients from the inventory seamlessly.
* 🛒 **Automated Purchase Orders:** The system allows users to generate new purchase orders when inventory is low. Staff can select items, specify quantities, choose suppliers, and track delivery statuses.
* 💻 **POS Dashboard Interface:** A responsive interface allows baristas and cashiers to record daily sales efficiently. These transactions automatically update the inventory levels in the background.
* 📊 **Reporting & Analytics:** The system generates visual analytics for total revenue and total costs. These metrics can be filtered by daily, weekly, or monthly views to support data-driven business decisions.
* 🔐 **Role-Based Access Control:** Secure login portals separate access for shop owners and general employees. Administrators can manage user groups and assign specific permissions.

---

## 🛠️ Tech Stack

### Frontend
* **Core Technologies:** HTML, CSS, JavaScript

### Backend
* **Language:** PHP
* **Development Environment:** XAMPP

### Database
* **Database System:** MySQL (Structured with strict 3NF normalization)
* **Management Interface:** phpMyAdmin

---

## 🏗️ Architecture & Methodology

This project was developed using the **Waterfall Model**, processing sequentially through requirement analysis, schema design, development, and system testing. 

The system utilizes a client-server architecture where the custom PHP web application handles business logic and communicates securely with the centralized MySQL database. Database connectivity is established using PHP Data Objects (PDO) with built-in exception handling to ensure robust data synchronization and security.

---

## 📸 Screenshots
*(Note: Upload your UI screenshots to an `assets` folder in your GitHub repo to display them here)*

| Login Portal | Main Dashboard | Inventory Tracking | Purchase Orders |
| :---: | :---: | :---: | :---: |
| <img src="assets/login.jpeg" width="200"/> | <img src="assets/dashboard.jpeg" width="200"/> | <img src="assets/inventory.jpeg" width="200"/> | <img src="assets/orders.jpeg" width="200"/> |

---

## 📥 How to Run Locally

> **⚠️ Academic Project Disclaimer:** 
> *This system is an academic project developed for the BIC21404 Database course at Universiti Tun Hussein Onn Malaysia (UTHM).* 

**Step 1: Environment Setup**
* Download and install [XAMPP](https://www.apachefriends.org/index.html).
* Start the **Apache** and **MySQL** modules from the XAMPP Control Panel.

**Step 2: Database Initialization**
* Open your browser and navigate to `http://localhost/phpmyadmin`.
* Create a new database named `coffee_secangkir`.
* Import the provided `.sql` database schema file into this new database. 

**Step 3: Run the Application**
* Clone this repository into your XAMPP `htdocs` directory (e.g., `C:\xampp\htdocs\secangkir`).
* Open your web browser and navigate to `http://localhost/secangkir` to access the login portal.
