# Metricalo Test Project

This project is a PHP application that runs using Docker with Nginx and PHP-FPM. No database is required.

### 1. Go to project folder after Cloning the Repository

cd metricalo-test

### 2. Make Sure You Have Docker Installed

Install Docker
Install Docker Compose

### 3. Build and Run the Project

docker compose up --build

This will:

Build the PHP container from ./docker/Dockerfile
Start Nginx using the config from ./docker/nginx/default.conf

### 4. Access the App

Open your browser and go to:
http://localhost:8080


## Usage

## API Example

You can trigger a payment via the API by sending a `POST` request to the following endpoint:

### Endpoint

ACI example
curl -X POST http://localhost:8080/api/payment/aci \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 100,
    "currency": "USD",
    "card_number": "4200000000000000",
    "card_holder": "Sherif Hesham",
    "exp_month": 11,
    "exp_year": 2030,
    "cvc": "123"
}'

Shift4 example
curl -X POST http://localhost:8080/api/payment/shift4 \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 100,
    "currency": "USD",
    "card_number": "4200000000000000",
    "card_holder": "Sherif Hesham",
    "exp_month": 11,
    "exp_year": 2030,
    "cvc": "123"
}'

# Json Response Ex:
{
    "success": true,
    "message": "Payment completed",
    "data": {
        "transactionId": "8ac7a4a19865c4430198718e04dd36ab",
        "createdAt": "2025-08-03T20:09:49+00:00",
        "amount": 100,
        "currency": "EUR",
        "cardBin": "420000"
    }
} 

## Command example

You can trigger a test payment using the CLI command below:

```bash
    bin/console app:payment-charge aci \
  --amount=100 \
  --currency=USD \
  --card-number=4200000000000000 \
  --card-holder="Sherif Hesham" \
  --exp-month=11 \
  --exp-year=2030 \
  --cvc=123
```

Parameters:
    --amount – Transaction amount.
    --currency – Transaction currency (e.g., USD, EUR).
    --card-number – Test card number.
    --card-holder – Cardholder full name.
    --exp-month – Expiration month (2 digits).
    --exp-year – Expiration year (4 digits).
    --cvc – Card verification code.


