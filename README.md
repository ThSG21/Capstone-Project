# Appointment Booking System

## Overview
This project is a web-based appointment booking app built using mainly PHP and MySQL.  
Users can create accounts, log in, and book appointments with stylists at available time slots.

---

## Features
- User registration and login system
- Password hashing for security
- Forgot password recovery using security question
- Multi-step appointment booking process
- Stylist selection
- Time slot scheduling
- Appointment conflict prevention
- View booked appointments

---

## How the System Works

### 1. User Authentication
Users create an account and log in. Session variables are used to keep the user logged in across pages.

### 2. Booking Process
The booking system works in 4 stages:
- Stage 1: Welcome screen
- Stage 2: Select services and stylist
- Stage 3: Choose date and time
- Stage 4: Confirm appointment

### 3. Database Interaction
All data is stored in a MySQL database:
- users table → stores login and profile data
- stylist table → stores stylist names
- appointments table → stores booking information

### 4. Appointment Validation
Before inserting a booking, the system checks if the selected stylist already has an appointment at that time.

---

## Technologies Used
- PHP
- MySQL
- JavaScript
- HTML/CSS
- Bootstrap

---

## Author
Diego Dominguez