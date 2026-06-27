# 👓 Clarity Optical Store Management System

A full-stack **Optical Store Management System** developed using **PHP, PostgreSQL, Bootstrap 5, JavaScript, HTML, and CSS**. The system digitalizes the day-to-day operations of an optical store by managing customers, prescriptions, products, suppliers, orders, deliveries, appointments, invoices, and reports through an interactive administrator dashboard.

---

# 📌 Project Overview

The Optical Store Management System is designed to simplify optical store operations by integrating customer management, inventory, prescription handling, order processing, payment tracking, delivery management, and business analytics into one centralized application.

Unlike a normal retail management system, this project also supports **optical prescriptions**, allowing the store to maintain detailed eye measurements for customers.

---

# ✨ Features

## 🔐 Authentication

* Administrator Login
* Administrator Registration
* Password Hashing
* Session Management
* Protected Admin Pages

---

## 👥 Customer Management

* Add Customer
* Edit Customer
* Delete Customer
* Search Customers
* Pagination
* Export Customer Report

---

## 👓 Product Management

* Add Products
* Edit Products
* Delete Products
* Product Categories
* Supplier Information
* Product Images
* Low Stock Monitoring

---

## 📦 Inventory Management

* Product Stock Tracking
* Automatic Stock Updates
* Low Stock Notifications
* Inventory Reports

---

## 🏥 Prescription Management

Each customer can have an optical prescription containing:

### Right Eye (OD)

* Sphere (SPH)
* Cylinder (CYL)
* Axis
* Addition (ADD)

### Left Eye (OS)

* Sphere (SPH)
* Cylinder (CYL)
* Axis
* Addition (ADD)

Doctor name and prescription date are also maintained.

---

## 📅 Appointment Management

* Book Appointment
* Appointment Status
* Doctor Name
* Appointment Notes

---

## 🛒 Order Management

* Create Orders
* Edit Orders
* Delete Orders
* Automatic Stock Reduction
* Order History
* Payment Status
* Payment Mode

---

## 💳 Payment Management

* Cash
* UPI
* Card

Payment records are automatically maintained for paid orders.

---

## 🚚 Delivery Management

Supports both:

* 🏠 Home Delivery
* 🏪 Store Pickup

Features include:

* Courier Selection
* Tracking Number
* Delivery Status
* Expected Delivery Date
* Delivered Date
* Delivery Address

---

## 🧾 Invoice Generation

Generate professional invoices containing:

* Store Details
* Customer Information
* Ordered Products
* Payment Status
* Total Amount

Invoices can be downloaded as PDF.

---

## 📊 Reports & Analytics

Business dashboard includes:

* Total Customers
* Total Products
* Total Suppliers
* Total Orders
* Total Revenue
* Low Stock Products

Analytics includes:

* Monthly Revenue
* Top Selling Products
* Sales Summary
* Stock Summary

---

## 🔔 Notifications

Automatic notifications for:

* Low Stock Products
* Inventory Alerts

---

## 📝 Audit Logging

Database triggers automatically record important changes such as:

* Product Updates
* Payment Changes

Audit logs store:

* User
* Table Name
* Action
* Timestamp

---

# 🗄 Database Modules

* Users
* Customers
* Products
* Categories
* Suppliers
* Orders
* Order Items
* Payments
* Deliveries
* Prescriptions
* Appointments
* Notifications
* Audit Logs

---

# 🚀 Advanced PostgreSQL Features

This project demonstrates advanced PostgreSQL concepts including:

* Foreign Key Constraints
* CHECK Constraints
* ON DELETE CASCADE
* ON DELETE RESTRICT
* Triggers
* Audit Logging
* Aggregate Functions
* Joins
* Transactions

---

# 💻 Technologies Used

### Frontend

* HTML5
* CSS3
* Bootstrap 5
* JavaScript

### Backend

* PHP

### Database

* PostgreSQL

### Libraries

* Bootstrap Icons
* Chart.js
* Dompdf

---

# 📂 Project Structure

```
os/

│── admin/
│── analytics/
│── appointments/
│── audit/
│── auth/
│── config/
│── customers/
│── deliveries/
│── includes/
│── invoices/
│── notifications/
│── orders/
│── prescriptions/
│── products/
│── reports/
│── suppliers/
│── uploads/
│── vendor/
│── assets/
│── index.php
```

---

# ⚙ Installation

## Clone Repository

```bash
git clone https://github.com/Reena005/optical_store_db.git
```

Move the project to:

```
C:\xampp\htdocs\
```

---

## Configure PostgreSQL

Create a database:

```sql
CREATE DATABASE optical_store;
```

Import the SQL schema.

---

## Configure Database Connection

Update:

```
config/database.php
```

with your PostgreSQL credentials.

---

## Start Server

Start:

* Apache (XAMPP)
* PostgreSQL

Open:

```
http://localhost/os
```

---

# Screenshots

Add screenshots here:

* Login Page
* Dashboard
* Customers
* Products
* Orders
* Prescriptions
* Deliveries
* Reports
* Analytics
* Invoice PDF

---

# Future Enhancements

* Online Customer Portal
* SMS Notifications
* Email Notifications
* QR Code Invoice
* Barcode Scanning
* Online Payment Gateway
* Cloud Deployment (AWS)

---

# Learning Outcomes

This project demonstrates practical knowledge of:

* Database Design
* Relational Database Management
* CRUD Operations
* Authentication
* PostgreSQL
* Transactions
* Triggers
* Audit Logging
* Inventory Management
* Report Generation
* Full Stack Web Development

---

# Author

**Reena Suruliraj**

B.E. Information Technology

Full Stack & PostgreSQL Developer

GitHub: https://github.com/Reena005
