# 📝 Notes Web App
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MongoDB](https://img.shields.io/badge/MongoDB-4EA94B?style=for-the-badge&logo=mongodb&logoColor=white)


This project is a Full Stack Notes Web Application that allows users to create, read, update, and delete notes. It features user authentication and a modern, responsive UI built with HTML, CSS, JavaScript, and Bootstrap. The backend is developed using PHP with MongoDB as the database.


## ✨ Features

- **📋 Note Management** - Create, read, update, and delete notes
- **🔐 User Authentication** - Secure registration and login system
- **📱 Responsive Design** - Optimized for both desktop and mobile devices
- **🔄 Real-time Updates** - Changes reflect immediately without page reloads
- **📊 Note History** - Track changes to your notes over time
- **🎨 Modern UI** - Clean, intuitive interface with animations
- **🔒 Secure Storage** - All data stored securely in MongoDB

## 🖥️ Screenshots
[Watch Screen-Record of Website](/htdocs/others/notesWebApp_Video.webm)

<table>
  <tr>
    <td><img src="/htdocs/others/home.png" alt="Home" width="100%"></td>
  </tr>
  <tr>
    <td><img src="/htdocs/others/sigup.png" alt="signup" width="100%"></td>
    <td><img src="/htdocs/others/login.png" alt="login" width="100%"></td>
  </tr>
   <tr>
    <td><img src="/htdocs/others/dashboard_dark.png" alt="dashboard-dark" width="100%"></td>
    <td><img src="/htdocs/others/dashboard_white.png" alt="dashboard-white" width="100%"></td>
  </tr>
  <tr>
    <td><img src="/htdocs/others/notes_history.png" alt="notes-history" width="100%"></td>
  </tr>
   <tr>
</table>

## 🛠️ Technologies Used

### Frontend
- **HTML5/CSS3** - Structure and styling
- **JavaScript** - Client-side functionality
- **Bootstrap 4** - Responsive design framework
- **Font Awesome** - Icon library
- **Particles.js** - Interactive background effects

### Backend
- **PHP 7.4+** - Server-side processing
- **MongoDB** - NoSQL database
- **Composer** - PHP dependency management

## 🏗️ Project Structure

```
notes-web-app/
├── htdocs/                     # Web root directory
│   ├── api/                    # API endpoints
│   │   ├── classes/            # PHP classes
│   │   └── config/             # Configuration files
│   ├── css/                    # Stylesheets
│   ├── js/                     # JavaScript files
│   ├── dashboard.html          # Main application interface
│   ├── index.html              # Landing page
│   ├── login.html              # Login page
│   ├── notes-history.html      # Note history page
│   ├── register.html           # Registration page
│   └── verify_mongodb.php      # Database connection test
├── mongodb/                    # MongoDB schemas and setup
│   └── schemas/
├── workspace/                  # Development workspace
│   ├── api/                    # API development files
│   ├── src/                    # Source files
│   │   ├── js/                 # JavaScript source
│   │   └── css/                # CSS source
│   └── vendor/                 # Dependencies
└── README.md                   # Project documentation
```

## 🚀 Getting Started

### Installation

1. **Install PHP**
    ```bash
    sudo apt update -y
    sudo apt install php -y
    ```

2. **Set up MongoDB Community Edition**
   - Install and start MongoDB service

   ```bash
   sudo apt update -y
   sudo apt-get install gnupg curl -y

   curl -fsSL https://www.mongodb.org/static/pgp/server-8.0.asc | \
   sudo gpg -o /usr/share/keyrings/mongodb-server-8.0.gpg \
   --dearmor

   echo "deb [ arch=amd64,arm64 signed-by=/usr/share/keyrings/mongodb-server-8.0.gpg ] https://repo.mongodb.org/apt/ubuntu noble/mongodb-org/8.0 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-8.0.list

   sudo apt-get update -y

   sudo apt-get install mongodb-org -y
   ```

   - Enable & Start MongoDB

   ```bash
   sudo systemctl enable mongod

   sudo systemctl start mongod

   sudo systemctl status mongod
   ```

3. **Clone the repository**
   ```bash
   git clone https://github.com/kabilyugan11/notes-web-app.git
   cd notes-web-app
   ```


4. **Navigate to the `htdocs` directory to Run the Web App**
   ```bash
   cd htdocs
   ```
   ```bash
   php -S localhost:8000 
   ```

### Running the Application

Open your web browser and navigate to `http://localhost:8000`
