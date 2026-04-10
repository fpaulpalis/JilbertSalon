# 💇 Jilbert Salon — Appointment Scheduler

> *Your Types. Your Style. Your Color.*

A web-based appointment scheduling system for Jilbert Salon, allowing clients to book services online and admins to manage appointments, services, and generate sales reports.

---

## ⚙️ Tech Stack

| Layer    | Technology                                  | 
|----------|---------------------------------------------| 
| Frontend | HTML5, CSS3, Bootstrap 5.3, Bootstrap Icons | 
| Backend  | PHP (procedural + PDO)                      | 
| Database | MySQL (MySQLi + PDO)                        | 
| Email    | EmailJS (Browser SDK v4)                    | 
| Charts   | Chart.js 4.4                                | 
| Fonts    | Google Fonts – Red Hat Display              | 
| Server   | Apache (XAMPP / LAMP)                       | 

---

## 📋 Appointment Booking Flow

```js 
Client fills form → PHP inserts to DB (Status: Pending)
       ↓
Success modal shown with Appointment Number
       ↓
EmailJS sends confirmation email to client
       ↓
Admin reviews → updates Status (Confirmed / Completed / Cancelled)
```

The booking form collects: Name, Email, Phone, Service, Date, and Time. On success, a confirmation modal appears with the generated appointment number, and an automatic email is sent to the client via EmailJS.

---

## ✉️ EmailJS Integration

EmailJS sends booking confirmation emails directly from the browser — no backend mail server needed.

**Setup (in**

```js 
emailjs.init("YOUR_PUBLIC_KEY");

emailjs.send("YOUR_SERVICE_ID", "YOUR_TEMPLATE_ID", {
    to_email:           appointmentData.customer_email,
    to_name:            appointmentData.customer_name,
    appointment_number: appointmentData.appointment_number,
    service:            appointmentData.service,
    appointment_date:   appointmentData.appointment_date,
    appointment_time:   appointmentData.appointment_time,
    salon_name:         'Jilbert Salon',
    salon_phone:        '+63 991 260 9479',
    salon_address:      '911 Asingan st., Florida, ...'
});
```

**To configure:**

1. Create a free account at [emailjs.com](https://www.emailjs.com)
2. Add an Email Service (Gmail, Outlook, etc.)
3. Create an Email Template using the variable names above
4. Replace the keys in `appointment.php`:
    1. `emailjs.init("YOUR_PUBLIC_KEY")`
    2. `service_xxxxxxx` → your Service ID
    3. `template_xxxxxxx` → your Template ID

---

## 🖥️ Admin Panel

Access via `/admin/index.php`. Default credentials: `admin` / `admin`.

### Dashboard
- Stat cards: Total, Completed, Pending/Confirmed, and Cancelled appointment counts
- **Line chart** — monthly appointment trend (last 6 months)
- **Bar chart** — top services by completion count
- **Doughnut chart** — appointment status breakdown
- Today's appointment list (up to 5 entries)

### Appointments
- Paginated table of all appointments (10 per page)
- Search by name, email, or appointment number
- Filter by status (Pending / Confirmed / Completed / Cancelled)
- Sort by any column
- Actions: **View**, **Edit**, **Delete**

### View Appointment
- Full appointment details with color-coded status badge
- Shows Remark and Remark Date if set by admin

### Edit Appointment
- Update status, date, time, service, and remarks

### Services
- **Add Service** — name, description, price (₱)
- **Manage Services** — searchable/sortable table with Edit and Delete
- Delete triggers a confirmation modal before removing

### Sales Report
- Filter by date range and status
- Shows appointment list with service cost per entry
- Calculates **Total Appointments** and **Total Revenue (₱)**
- Export to **CSV**
- **Print-ready** layout via `window.print()`

---

## ✨ Client-Side Pages

| Page              | Description                                    | 
|-------------------|------------------------------------------------| 
| `index.php`       | Home with image carousel and About section     | 
| `service.php`     | Lists available salon services                 | 
| `appointment.php` | Booking form + success modal + EmailJS trigger | 
| `contact.php`     | Contact information                            | 

---

## 🗄️ Database

**File:** `includes/database.sql`

| Table            | Description                                | 
|------------------|--------------------------------------------| 
| `tbladmin`       | Admin login credentials                    | 
| `tblappointment` | All appointments with status tracking      | 
| `tblcustomers`   | Customer profiles                          | 
| `tblservices`    | Services with name, description, and price | 

---

## 🚀 Setup
1. Extract project into your web server root (e.g., `htdocs/Jilbert-Salon/`)
2. Import `includes/database.sql` into MySQL
3. Update DB credentials in `includes/dbconnection.php`
4. Configure EmailJS keys in `appointment.php`
5. Visit `http://localhost/Jilbert-Salon/`

---

## 👨‍💻 Authors

| Name                         | 
|------------------------------| 
| Genuino, Sir Xziann Jeano P. | 
| Palis, Francis Paul A.       | 
| Soberano, Christian Adel S.  | 

**Course:** ITE 314 — Advanced Database Systems | P3 PeTa & Exam
**Submitted to:** Sir Rofer Junio Savella
**Date:** November 1, 2025
