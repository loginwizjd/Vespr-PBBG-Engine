## Vespr
## An Open Source PHP Framework/Engine for PBBG-Style Games

Welcome to the repository for Vespr, our PHP-based framework/engine designed specifically for Persistent Browser-Based Games (PBBGs). This framework is built to provide a solid foundation for developing and managing PBBG-style games, but its flexibility also allows it to be utilized as a robust backend for any website.

### What is a PBBG?

A Persistent Browser-Based Game (PBBG) is a game genre characterized by its continuous and long-term gameplay, where players interact within a persistent world. These games often involve complex player interactions, resource management, and strategic decision-making, all happening in real-time.

### Key Features

- **Flexible Framework:** Designed with modularity in mind, this framework can serve as the backbone for a full-featured PBBG or be adapted for any web-based application.
- **Plugin Support:** Extend your game's functionality with our plugin system. Plugins can add new features, modify existing ones, or completely overhaul the game's mechanics.
- **Database Integration:** Easily connect your game to a MySQL database. Our framework includes all the necessary SQL scripts to set up your game's database schema quickly.
- **User Management:** Built-in user management system, including roles, permissions, and authentication, making it easier to manage players or website users.
- **Event Handling:** Robust event-handling system to manage in-game events, user actions, and notifications.
- **Customizable:** Highly customizable to fit the needs of your game or website, with options to add your own scripts, styles, and plugins.

### Getting Started

To get your PBBG or website up and running using this framework, follow these steps:

1. **Upload the SQL File:** 
   - Navigate to the `includes` directory in this repository.
   - Upload the `vespr.vip.sql` file to your MySQL database. This file contains the necessary tables and initial data to get your application started.

2. **Configure Database Connection:**
   - Open the `db.php` file located in the `includes` directory.
   - Set the necessary database connection information, such as the hostname, database name, username, and password.

   ```php
   <?php
   $host = 'localhost'; // Your database host
   $db = 'your_database_name'; // Your database name
   $user = 'your_username'; // Your database username
   $pass = 'your_password'; // Your database password

   try {
       $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
       $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   } catch (PDOException $e) {
       echo 'Connection failed: ' . $e->getMessage();
   }
   ?>
   ```

3. **Launch Your Game or Website:**
   - Once the database is configured, you can start developing your game or website using the provided framework.
   - The framework includes various templates and scripts that you can modify to suit your specific needs.

### Plugins and Guides

To extend the functionality of your PBBG or website, you can find a wide range of plugins and guides available at [https://vespr.vip/index.php](https://vespr.vip/index.php). This resource will help you integrate new features, troubleshoot issues, and customize your application to your liking.

### Contributing

We welcome contributions to improve this framework. If you have ideas for new features, find bugs, or want to improve existing functionality, feel free to submit a pull request or open an issue.

### License

This project is open-source and licensed under the [MIT License](LICENSE). You are free to use, modify, and distribute this framework as per the terms of the license.

---

Thank you for using our Vespr framework for PBBG-style games. We look forward to seeing the amazing games and websites you create with it!
