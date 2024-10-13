# Personal Finance Manager

## Overview

Personal Finance Manager is a web-based application built with PHP that helps users track their income, expenses, and overall financial health. This app provides a user-friendly interface for managing personal finances, setting budgets, and generating financial reports.

## Features

- User authentication (register, login, logout)
- Dashboard with financial summary
- Add, edit, and delete transactions
- Categorize transactions
- Set and manage budgets
- Generate financial reports with charts
- Search and filter transactions

## Technologies Used

- PHP 7.4+
- MySQL 5.7+
- HTML5
- CSS3
- JavaScript
- Chart.js for data visualization

## Installation

1. Clone the repository:
   ```
   git clone https://github.com/kelvin-amaju/finance_manager.git
   ```

2. Set up a local web server (e.g., XAMPP, WAMP, or MAMP) and ensure PHP and MySQL are installed.

3. Create a new MySQL database named `finance_app`.

4. Import the database schema from `finance_manager.sql` (you'll need to create this file with the necessary SQL statements).

5. Update the database connection details in `config/database.php`:
   ```php
   $host = 'localhost';
   $db   = 'finance_manager';
   $user = 'your_username';
   $pass = 'your_password';
   ```

6. Place the project files in your web server's document root or a subdirectory.

7. Access the application through your web browser (e.g., `http://localhost/finance_manager`).

## Usage

1. Register a new account or log in with existing credentials, (Username: Victory, Password: 1234567890).
2. Use the dashboard to get an overview of your financial status.
3. Add new transactions using the "Add Transaction" page.
4. View and manage your transactions on the "View Transactions" page.
5. Set up budgets using the "Manage Budgets" page.
6. Generate financial reports and visualize your data on the "Reports" page.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is open source and available under the [MIT License](LICENSE).

## Contact

If you have any questions or suggestions, please open an issue or contact the maintainer at kelvinin112@gmail.com.
