# API PHP Project

This project provides a RESTful API for managing records in the `contract`,`customer`,`user` and `room` tables. The API is built using PHP and interacts with a MySQL database.

## Project Structure

```
api-dic
├── rest-api
│   ├── api
│   │   ├── add_contract.php
│   │   ├── update_contract.php
│   │   ├── delete_contract.php
│   │   ├── read_contract.php
│   │   ├── add_room.php
│   │   ├── update_room.php
│   │   ├── delete_room.php
│   │   └── read_room.php
            add_user.php
            update_user.php
            delete_user.php
            read_user.php
            add_customer.php
            update_customer.php
            delete_customer.php
            read_customer.php
│   ├── class
│   │   ├── contract.php
│   │   └── room.php    
            user.php
            customer.php
│   └── config
│       └── database.php
└── README.md
```

## API Endpoints

### contract Endpoints

- **Add contract**
  - **Endpoint:** `/api/add_contract.php`
  - **Method:** POST
  - **Description:** Creates a new record in the `contract` table.

- **Update contract**
  - **Endpoint:** `/api/update_contract.php`
  - **Method:** PUT
  - **Description:** Updates an existing record in the `contract` table.

- **Delete contract**
  - **Endpoint:** `/api/delete_contract.php`
  - **Method:** DELETE
  - **Description:** Deletes a record from the `contract` table.

- **Read contract**
  - **Endpoint:** `/api/read_contract.php`
  - **Method:** GET
  - **Description:** Retrieves records from the `contract` table.

### room Endpoints

- **Add room**
  - **Endpoint:** `/api/add_room.php`
  - **Method:** POST
  - **Description:** Creates a new record in the `room` table.

- **Update room**
  - **Endpoint:** `/api/update_room.php`
  - **Method:** PUT
  - **Description:** Updates an existing record in the `room` table.

- **Delete room**
  - **Endpoint:** `/api/delete_room.php`
  - **Method:** DELETE
  - **Description:** Deletes a record from the `room` table.

- **Read room**
  - **Endpoint:** `/api/read_room.php`
  - **Method:** GET
  - **Description:** Retrieves records from the `room` table.

### customer Endpoints

- **Add customer**
  - **Endpoint:** `/api/add_customer.php`
  - **Method:** POST
  - **Description:** Creates a new record in the `customer` table.

- **Update customer**
  - **Endpoint:** `/api/update_customer.php`
  - **Method:** PUT
  - **Description:** Updates an existing record in the `customer` table.

- **Delete customer**
  - **Endpoint:** `/api/delete_customer.php`
  - **Method:** DELETE
  - **Description:** Deletes a record from the `customer` table.

- **Read customer**
  - **Endpoint:** `/api/read_customer.php`
  - **Method:** GET
  - **Description:** Retrieves records from the `customer` table.

### user Endpoints

- **Add user**
  - **Endpoint:** `/api/add_user.php`
  - **Method:** POST
  - **Description:** Creates a new record in the `user` table.

- **Update user**
  - **Endpoint:** `/api/update_user.php`
  - **Method:** PUT
  - **Description:** Updates an existing record in the `user` table.

- **Delete user**
  - **Endpoint:** `/api/delete_user.php`
  - **Method:** DELETE
  - **Description:** Deletes a record from the `user` table.

- **Read user**
  - **Endpoint:** `/api/read_user.php`
  - **Method:** GET
  - **Description:** Retrieves records from the `user` table.

## Database Configuration

The database connection is configured in the `rest-api/config/database.php` file. Ensure that the database credentials are set correctly to establish a connection.

## Usage

To use the API, send HTTP requests to the specified endpoints with the appropriate method and data format (JSON). Make sure to handle CORS and content type headers as needed.

## Setup Instructions

1. Clone the repository.
2. Configure the database connection in `rest-api/config/database.php`.
3. Deploy the API on a PHP server.
4. Test the endpoints using tools like Postman or cURL.