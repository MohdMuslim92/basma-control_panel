# Basma Organization Management System

## Introduction
Welcome to the Basma Organization Management System, a comprehensive solution designed to streamline the operations of Basma, a Sudanese charity organization dedicated to supporting orphans and raising community awareness. This system helps manage members, track orphan data, and assist in decision-making based on the collected information, making the organization's work more efficient and its records secure for future use.

* Deployed Site: [Basma Organization Management System](#)
* Project Blog Article: [Read the Project Blog](#)
* My LinkedIn: [Mohammed Muslim Saeed](https://linkedin.com/in/mohdmuslim92)

## Inspiration and Story
As a former member of the Basma organization, I wanted to make a significant impact and leave my mark. This project aims to modernize the organization's operations by providing a user-friendly website that serves both the public and the organization internally. Inspired by the challenges of traditional systems—paper records prone to loss, damage, and inefficiencies. I set out to create a digital solution that ensures data is well-preserved and easily accessible.

Developing this project was a journey filled with learning and overcoming challenges. From setting up the database to integrating Vue.js for a seamless user experience, every step was an opportunity to grow and innovate. My vision for the next iteration includes adding more analytical tools, improving the user interface, and expanding the system's capabilities to better serve the organization's needs.

## Implemented Features:
* jsPDF: For generating and downloading reports and data.
* i18n: To support English and Arabic translations, making the system accessible to a broader audience.
* Vue.js: For a dynamic and responsive user interface.
* Laravel: To handle the backend logic and database interactions.


## Installation

### Prerequisites
Ensure you have the following installed:

* PHP
* Composer
* Node.js and npm
* Database (e.g., MySQL, PostgreSQL, SQLite)

**Steps**

* Install Laravel:

    ```
        composer global require laravel/installer
    ```

* Clone the repository and configure it:
    ```
        git clone https://github.com/MohdMuslim92/basma-control_panel.git
        cd basma-control_panel
        composer install
    ```

* Install Frontend Dependencies and Vue.js:

    ```
        npm install
    ```

* Database Setup:

    Create a database for your project.
    Configure .env with database credentials:
    ```
        cp .env.example .env
    ```
    Update the .env file with your configuration.

* Run migrations:

    ```
        php artisan migrate
    ```

* Start Development Server:

    ```
        php artisan serve
    ```

* Compile Assets with Laravel Mix:

    ```
        npm run dev
    ```

## Usage
This project provides both an interface for visitors and an administrative system to manage the organization's operations. Features include:

* Member management
* Orphan data collection and tracking
* Decision-making tools based on saved data
* Secure and efficient record-keeping

## Contributing
We welcome contributions to enhance the Basma Organization Management System. To contribute:

1. Fork the repository.
2. Create a new branch (git checkout -b feature-branch).
3. Commit your changes (git commit -m 'Add some feature').
4. Push to the branch (git push origin feature-branch).
4. Open a Pull Request.

## Related Projects
[Basma Charity Organization](https://basmaorphans.com)

## Licensing
This project is licensed under the MIT License - see the LICENSE file for details.


I hope this project demonstrates my dedication to making meaningful contributions and my ability to tackle real-world problems with practical solutions.



Thank you for taking the time to explore this project. If you have any questions or feedback, feel free to reach out!
