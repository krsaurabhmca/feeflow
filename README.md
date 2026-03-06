# FeeFlow - Modern School Fee Management System

FeeFlow is a premium, lightweight, and responsive school fee management platform designed for educational institutes. It streamlines the process of managing classes, student records, fee categories, and fee collection with a focus on speed and user experience.

![Banner](assets/img/banner.png)

## 🚀 Key Features

- **Dynamic Dashboard**: Real-time insights into total collections, student counts, and recent transactions.
- **Class & Course Management**: Easily organize your institute's offerings.
- **Flexible Fee Categories**: Define various fee types (Monthly, Admission, Examination, etc.).
- **Smart Student Profiles**: Manage student data, academic records, and fee history.
- **Rapid Fee Collection**: Simplified workflow for processing payments and generating receipts.
- **Professional Receipts**: Automated PDF receipt generation with branding.
- **Advanced Analytics**: Detailed reports and collection summaries.
- **Fully Responsive**: Optimized for both desktop and mobile devices.

## 🛠️ Technology Stack

- **Backend**: PHP 8.x
- **Database**: MySQL / MariaDB
- **Frontend**: Vanilla CSS3, HTML5
- **Icons**: Font Awesome 6
- **Typography**: Inter (Google Fonts)
- **PDF Generation**: FPDF / Custom Helper

## 📋 Installation Guide

### Prerequisites
- XAMPP / WAMP / LAMP stack
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Steps
1. **Clone the repository**:
   ```bash
   git clone https://github.com/yourusername/feeflow.git
   ```
2. **Setup Database**:
   - Create a database named `feeflow_db`.
   - Import the `includes/database.sql` file into your MySQL server.
3. **Configure Environment**:
   - The application automatically switches between local and live server settings in `includes/config.php`.
   - Update your local/live database credentials if necessary.
4. **Run Application**:
   - Copy the project folder to your `htdocs` or equivalent directory.
   - Access it via `http://localhost/feeflow/`.

## ⚙️ Configuration

The `includes/config.php` file handles environment-specific settings:

```php
// Automatic environment detection
if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
    // Local Settings...
} else {
    // Live Server Settings...
}
```

## 📄 License
This project is licensed under the MIT License.

---
Developed with ❤️ by [Your Name/Company]
