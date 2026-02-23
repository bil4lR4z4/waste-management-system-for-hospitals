# Waste Management System for Hospitals Using Laravel

This is a **comprehensive web-based Waste Management System** built with **Laravel**, designed to manage hospital waste, employee roles, third-party contractors, inventory, and reporting efficiently.

## Roles and Permissions

The system has **three main roles**:

1. **Admin**  
   - Can create **managers (third-party users)**  
   - Assign user permissions as needed  
   - Full control over the system, including companies, inventory, invoices, and reports  

2. **Manager (Third-Party Contractor)**  
   - Created by **Admin**  
   - Can add **employees** (cannot create other managers)  
   - Can view and manage **only their own records**  
   - Handle contracts with hospitals or companies for waste collection and disposal  
   - Manage employees’ salary records, generate invoices, and maintain reports  

3. **Employee**  
   - Created by managers  
   - Can perform tasks assigned to them  
   - Do **not** have access to system-wide permissions or admin functionalities  

---

## Third-Party Waste Management

- Third-party companies can sign **contracts with hospitals or other companies** for waste collection.  
- They are responsible for **collecting and burning hospital waste** according to the contract.  
- Access is restricted to their **own records only** — they cannot see other companies’ data.  

---

## Inventory System

The system includes a **full inventory module**:

- Admin or third-party companies can:  
  - Purchase items  
  - Sell items  
  - Rent out items  
- Inventory operations are linked to invoices and reporting.  

---

## Features

- **Role-Based Access Control** (Admin, Manager, Employee)  
- **User Permission Management**  
- **Employee Management** (salary, tasks, reports)  
- **Company & Contract Management**  
- **Waste Collection & Disposal Tracking**  
- **Invoice Generation & Reports**  
- **Inventory Management** (purchase, sale, rent)  
- **Secure and scalable Laravel application**  

## Features
email : admin@gmail.com
password : admin@gmail.com

