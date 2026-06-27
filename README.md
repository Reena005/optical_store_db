Authentication
  * Login
  * Logout
  * Forgot Password
Customer
   * Add Customer
   * Edit Customer
   * Delete Customer
   * Search Customer
Prescription
   * Eye Power
   * Doctor Notes
   * Prescription History
   * Inventory
Frames
   * Lenses
   * Sunglasses
   * Contact Lens
Supplier
   * Add Supplier
   * Purchase Stock
Billing
   * Cart
   * Invoice
   * PDF Bill
Reports
   * Sales
   * Stock
   * Revenue
Dashboard
   * Today's Sales
   * Customers
   * Stock
   * Revenue

 Customer
      |
      |
Prescription
      |
Appointment

Supplier ---- Products ---- Inventory
                       |
                    Order Items
                       |
                     Orders
                       |
                    Payments









                    OpticalStore/
│
├── admin/
│   ├── dashboard.php
│   ├── customers.php
│   ├── products.php
│   ├── suppliers.php
│   ├── orders.php
│   ├── reports.php
│
├── auth/
│   ├── login.php
│   ├── logout.php
│
├── config/
│   └── database.php
│
├── includes/
│   ├── header.php
│   ├── sidebar.php
│   ├── footer.php
│   └── navbar.php
│
├── assets/
│   ├── css/
│   ├── js/
│   ├── images/
│
├── uploads/
├── database/
│   └── schema.sql
│
├── index.php
└── README.md


Email:
admin@gmail.com

Password:
admin123