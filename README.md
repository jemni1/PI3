# Data Farm - Smart & Sustainable Agriculture Platform with AI & ODD

<div align="center">
  <img src="./public/Front/images/logo11.png" alt="Data Farm Logo" width="400"/>
  <p><em>Revolutionizing agriculture with AI and sustainable practices</em></p>
</div>

## 📋 Overview

**Data Farm** is an intelligent web application developed as part of the **Esprit School of Engineering** university project.

The platform aims to revolutionize agricultural management through modern technologies by integrating:

- ✅ **Digital farm management system**
- ✅ **Intelligent automation of crops, lands, and resources**
- ✅ **Artificial Intelligence (YOLOv8) for plant disease and weed detection**
- ✅ **Sustainable Development Goals (SDG) tracking and implementation**

## 🌱 Sustainable Development Goals (SDG)

Data Farm actively contributes to the UN's Sustainable Development Goals:

### 🔋 **Goal 7: Affordable and Clean Energy**
- Promotion of renewable energy from recycled agricultural waste (biogas)

### ♻️ **Goal 12: Responsible Consumption and Production**
- Sustainable management of agricultural resources
- Resource optimization and waste reduction
- Efficient agricultural waste management with intelligent recycling processes

### 🌍 **Goal 13: Climate Action**
- Promotion of renewable energy and circular economy solutions
- Reduction of environmental impacts through intelligent and sustainable management

## ⭐ Key Features

### User Management
- Multi-role system (Admin, Farmer, Client, Worker)
- Authentication with secure login/signup
- Password reset functionality

### Land & Crop Management
- Digital parcel creation with precise geolocation
- Real-time temperature monitoring via Weather API
- Crop selection and monitoring tailored to land specifications

### Product Management
- Agricultural product listing and e-commerce functionality
- Order processing and tracking
- Sales statistics and reporting

### Waste Management
- Agricultural waste declaration and tracking
- Intelligent recycling allocation
- Waste collection PDF reports

### Equipment Management
- Equipment listing and availability tracking
- Reservation system for workers
- SMS notifications for reservation confirmations

### Communication
- Claim/support ticket system
- Email notifications for product sales
- QR code generation for orders

### AI-Powered Analysis
- **YOLOv8 Disease Detection**: Identifies unhealthy plants and crops
- **YOLOv8 Weed Classification**: Distinguishes between harmful and beneficial weeds

## 🛠️ Technologies

### Backend
- **Symfony 6.4** (PHP)
- **MySQL** database

### AI & Image Processing
- **Python 3.8+**
- **Ultralytics YOLOv8**
- Computer vision for plant analysis

### APIs & Services
- Weather API (geolocation and real-time temperature)
- Email services for notifications
- SMS gateway for reservation confirmations
- QR code generation

### Development Tools
- Composer
- GitHub Actions

## 📥 Installation

### Setting up the Symfony Backend

```bash
# Clone repository
git clone https://github.com/your-username/data-farm.git
cd data-farm

# Install PHP dependencies
composer install

# Configure your database connection in .env
DATABASE_URL="mysql://user:password@127.0.0.1:3306/datafarm_db"

# Run migrations
php bin/console doctrine:migrations:migrate

# Start Symfony server
symfony server:start
```

### Setting up Python & YOLOv8 for AI Features

```bash
# Install Python 3.8+ from https://www.python.org/downloads/

# Create and activate virtual environment
python -m venv venv

# On Windows
venv\Scripts\activate
# On macOS/Linux
source venv/bin/activate

# Update pip
pip install --upgrade pip

# Install Ultralytics (YOLOv8)
pip install ultralytics

# Verify installation
yolo
```

## 🚀 Usage

### For Farmers
1. Create your account and set up your farm profile
2. Add and manage your land parcels with geolocation
3. Select appropriate crops based on land characteristics
4. Monitor real-time weather conditions
5. List agricultural products for sale
6. Manage equipment and waste recycling

### For Clients
1. Browse available agricultural products
2. Place orders with secure checkout
3. Track order status

### For Workers
1. View available equipment
2. Make reservations for needed tools
3. Receive SMS confirmations

### For Administrators
1. Manage user accounts and permissions
2. Handle support tickets and claims
3. Access comprehensive statistics and reports

## 🔄 Development Workflow

### Running the Application
```bash
# Start Symfony server
symfony server:start

# In a separate terminal, run YOLOv8 services (if needed for development)
python ai_services/run_detection.py
```

### Testing
```bash
# Run Symfony tests
php bin/phpunit

# Run Python AI module tests
pytest ai_modules/tests/
```

## 🤝 Contributing

We welcome contributions to the Data Farm project!

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👏 Acknowledgments

- Esprit School of Engineering for project guidance
- YOLOv8 and Ultralytics for AI vision capabilities
- All contributors who have helped shape this project

---

<div align="center">
  <p>Developed with ❤️ by the Data Farm Team</p>
</div>
