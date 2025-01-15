# SuperConcert
SuperConcert is a web-based platform designed to managed and optimize ticket selling service and the organizer booking event platform. This system provide to **Admin** can **register event organiser**.  **Admin and Event Organiser** can **analysis reports**. **Event Organiser** can **event creation** and it **include** **ticket setup**. Attendees, can **Ticket booking** and after **include** **need to payment**, and **manage Waitlist**

# Features
- **Register event organiser:** Manage organiser account and record their contact information and credentials
- **Event Creation and Ticket setup:** Provide different types of ticket, and promotion codes or discount to attendees
- **Ticket Booking and Seat Management and Payment :** Let attendees to choose places, buy tickets, use promotion code, after make payment.
- **Event Check-In and Entry Management :** Generate QR for attendee, the QR is admission Ticket
- **Manage Waitlist :** Provide a platform to provide attendees have second change to buy ticket, when the ticket are sold in first time.
- **Analytics Reports :** Organiser can check the status of event, Admin can know auditorium usage

# Technology Stack
- **Front-end:** HTML, CSS, JavaScript
- **Back-end:** PHP
- **Database:** MySQL (via phpMyAdmin)
- **Hosting:** Localhost (XAMPP)

#Installation
1. **Install XAMPP :** Download and install [XAMPP](https://www.apachefriends.org/index.html) to set up a local server.

2. **Clone the Repository:**
   ```bash
   git clone https://github.com/JackYap123/SuperConcert.git
Set Up Database:

Open phpMyAdmin from XAMPP control panel.
Create a new database called SuperConcert.
Import the provided SQL file (SuperConcert.sql) into the new database.
Configure the Project:

Move the project folder into the htdocs directory in your XAMPP installation.
Ensure the database connection details are correct in the PHP configuration file (e.g., config.php).
Start the Server:

Open the XAMPP control panel and start Apache and MySQL.
Navigate to http://localhost/SuperConcert in your browser to view the project.
Usage




