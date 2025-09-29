# Car Rental – Full-Stack Web Application  

## Overview  
Car Rental is a **full-stack web application** for renting cars.  
It was built as a complete project covering both **backend (REST API)** and **frontend (SPA)**.  

## Technologies  

**Backend (REST API):**  
- PHP (FlightPHP microframework)  
- MySQL (XAMPP local server)  
- DAO + Service architecture  
- JWT authentication (Firebase JWT)  
- Swagger (OpenAPI) documentation  

**Frontend (SPA):**  
- HTML5, CSS3 (modern dark/neon UI)  
- JavaScript (jQuery)  
- SPApp (Single Page Application navigation)  
- Toastr.js (notifications)  
- AJAX (frontend-backend communication)  

---

## Features  

### 👤 Authentication & Users  
- User registration (password hashing)  
- JWT-based login  
- Role-based access (`admin`, `user`)  
- Profile page with password change & account deletion  

### 🛠️ Admin Dashboard  
- **Cars CRUD** – add, edit, activate/deactivate, delete  
- **Users CRUD** – view all users, activate/deactivate accounts  
- **Locations CRUD** – manage pickup/return locations  
- **Reservations CRUD** – view, confirm, or cancel reservations  

### 🚘 Cars  
- Display all cars  
- Filter by availability  
- Show only active cars  

### 📍 Rent Now  
- Dynamic dropdowns for cars & locations  
- Date validation (no overlapping reservations)  
- Automatic price calculation based on selected car & dates  
- Reservation stored in database with server-side validation  

### 📄 Profile  
- Display user info (first name, last name, email)  
- Change password (rehash stored in DB)  
- Show all user reservations with **cancel option**  

---

## Getting Started  

### 1. Clone repository  
```bash
git clone git@github.com:susicharis7/car-rental.git
cd car-rental
