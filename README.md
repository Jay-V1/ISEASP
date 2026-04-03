# ISEASP - Ilocos Sur Educational Assistance and Scholarship Program

A comprehensive scholarship management system for the Provincial Government of Ilocos Sur, Philippines. This system streamlines the entire scholarship process from application to graduation, including applicant tracking, requirements verification, examination management, interview scheduling, scholar renewal, and payroll management.

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [System Requirements](#system-requirements)
- [Installation Guide](#installation-guide)
- [Database Setup](#database-setup)
- [Configuration](#configuration)
- [User Roles](#user-roles)
- [Modules](#modules)
- [Usage Guide](#usage-guide)
- [Printing Features](#printing-features)
- [System Logs](#system-logs)
- [Troubleshooting](#troubleshooting)
- [Folder Structure](#folder-structure)
- [Contributing](#contributing)
- [License](#license)

## 🎯 Overview

ISEASP is a web-based scholarship management system designed to automate and streamline the scholarship process for Ilocos Sur scholars. The system handles applicant registration, requirements verification, examination scheduling, interview management, scholar renewal, and payroll disbursement.

## ✨ Features

### Core Features
- **Multi-Role User Management** (Super Admin, Admin, Evaluator, Staff)
- **Applicant Management** with complete profile tracking
- **Requirements Checklist** with verification system
- **Examination Management** with scheduling and results tracking
- **Interview Management** with scoring and recommendations
- **Scholar Renewal System** for yearly scholarship continuation
- **Payroll Management** for stipend disbursement
- **Activity Logging** for complete audit trail
- **Reporting System** with printable formats
- **School Year Management** with active year tracking

### Key Functionalities
- ✅ Applicant registration and tracking
- ✅ Requirements submission and verification
- ✅ Exam scheduling and result recording
- ✅ Interview scheduling and evaluation
- ✅ Scholarship award management
- ✅ Scholar renewal processing
- ✅ Payroll generation and disbursement
- ✅ Disbursement record keeping
- ✅ Activity monitoring and logging
- ✅ Municipality and district management

## 💻 System Requirements

### Server Requirements
- **PHP Version:** 7.4 or higher (PHP 8.2 recommended)
- **Web Server:** Apache / Nginx / XAMPP / WAMP
- **Database:** MySQL 5.7 or higher (MariaDB 10.4+)
- **Browser:** Chrome, Firefox, Edge, Safari (latest versions)

### Required PHP Extensions
- mysqli
- PDO
- GD (for image handling)
- fileinfo
- mbstring
- json

### Hardware Requirements
- **CPU:** 1.0 GHz or higher
- **RAM:** 2GB minimum (4GB recommended)
- **Storage:** 500MB for application + database growth

## 🚀 Installation Guide

### Method 1: Using XAMPP (Recommended for Local Development)

1. **Install XAMPP**
   - Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
   - Install XAMPP in default directory (C:\xampp)

2. **Clone or Download Project**
   ```bash
   cd C:\xampp\htdocs\
   git clone https://github.com/Jay-V1/ISEASP.git ISEASP
