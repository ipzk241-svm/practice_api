postman documentation for API: --- https://documenter.getpostman.com/view/41649057/2sAYX2Pjfv

# PHP API Setup Guide

## Prerequisites

Ensure you have the following installed:
- **PHP 8.1**
- **Symfony CLI**
- **Composer**

### Enable Required PHP Extensions:
- `curl` (optional)
- `openssl`
- `sodium`

## Setup Guide

### 1. Clone the Repository
```
git clone https://github.com/ipzk241-svm/practice_api
cd php-api
```

### 2. Install Dependencies
```
composer install
```

### 3. Generate JWT SSL Keypair
```
php bin/console lexik:jwt:generate-keypair
```

### 4. Start Symfony Server
```
symfony server:start
```
