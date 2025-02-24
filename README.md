
# Discount System - Laravel API

## Introduction

The Discount System is a Laravel API module designed to apply discounts to cart totals. It supports:

Percentage & Fixed Amount Discounts

Minimum Cart Total Requirements

Applicable to Specific Products or Categories

Valid Date Ranges

Stacking Multiple Discounts


## Installation

Steps to install project

```bash
git clone https://github.com/88khanm/discount-system.git  //clone repo

cd discount-system      //Change directory to Project Root Directory

composer install  //Install Dependencies

cp .env.example .env    //Configure Environment Variable (Copy .env.example to .env and update database details)
 
CREATE DATABASE discount_system;  //run this query on mysql or phpmyadmin you can use any database name in place of discount_system

php artisan migrate --seed     //Run migrations and seed data (after setup database run this command in  project root directory) 

php artisan serve   //Start Laravel Server
```
    
## Swagger OpenApi
Swagger is an open-source framework used for designing, documenting, and testing RESTful APIs. It provides an interactive UI where developers can explore API endpoints, send requests, and view responses—all without needing a separate tool like Postman.

i have integrated, you can access Swagger by navigating to 
    **http://localhost:<your-port>/api/documentation** 
Ensure that your application is running before accessing the Swagger interface.

Note:
In case the Swagger interface is not accessible, I have also documented all API routes, including their endpoints and expected responses, as a backup. This ensures you can reference the API structure and responses even if Swagger is unavailable.

#### Get discounts list

```http
  GET /api/discounts
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `NULL` | `NULL` | Return discounts list |

#### Create Discount

```http
  POST /api/discounts/store
```

| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `code`      | `string` | **Required**.  |
| `type`      | `string` | **Required**.  |
| `value`      | `decimal` | **Required**.  |
| `min_cart_total`      | `numeric` | **Optional**.  |
| `applicable_products`      | `array` | **Optional**.  |
| `applicable_categories`      | `array` | **Optional**.  |
| `active_from`      | `timestamp` | **Optional**.  |
| `active_to`      | `timestamp` | **Optional**.  |
| `stackable`      | `boolean` | **Optional**.  |

#### Discounts Apply

```http
  POST /api/discounts/apply
```

| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `cart_total`      | `decimal` | **Required**.  |
| `cart_items`      | `array` | **Required**.  |
| `discount_codes`      | `array` | **Optional**.  |






## Usage/Examples

```javascript

GET /api/discounts End point

### Reponse body ###

status code 201
{
  "data": [
    {
      "id": 1,
      "code": "WELCOME10",
      "type": "percentage",
      "value": "10.00",
      "max_uses": 100,
      "per_user_limit": 3,
      "first_time_only": 1,
      "active_from": "2025-01-01T00:00:00.000000Z",
      "active_to": "2025-12-31T23:59:59.000000Z",
      "min_cart_total": "50.00",
      "applicable_products": [
        1,
        2,
        3
      ],
      "applicable_categories": [
        5
      ],
      "expires_at": null,
      "created_at": "2025-02-23T12:21:55.000000Z",
      "updated_at": "2025-02-23T12:21:55.000000Z",
      "stackable": true
    },
    {
      "id": 2,
      "code": "ADDEXT10",
      "type": "percentage",
      "value": "10.00",
      "max_uses": null,
      "per_user_limit": null,
      "first_time_only": 0,
      "active_from": "2025-01-01T00:00:00.000000Z",
      "active_to": "2025-12-31T23:59:59.000000Z",
      "min_cart_total": "50.00",
      "applicable_products": [
        2,
        6
      ],
      "applicable_categories": [
        3,
        7
      ],
      "expires_at": null,
      "created_at": "2025-02-23T12:48:55.000000Z",
      "updated_at": "2025-02-23T12:48:55.000000Z",
      "stackable": true
    },
    {
      "id": 3,
      "code": "STCNT10",
      "type": "percentage",
      "value": "10.00",
      "max_uses": null,
      "per_user_limit": null,
      "first_time_only": 0,
      "active_from": "2025-01-01T00:00:00.000000Z",
      "active_to": "2025-12-31T23:59:59.000000Z",
      "min_cart_total": "50.00",
      "applicable_products": [
        2,
        6
      ],
      "applicable_categories": [
        3,
        7
      ],
      "expires_at": null,
      "created_at": "2025-02-23T12:53:08.000000Z",
      "updated_at": "2025-02-23T12:53:08.000000Z",
      "stackable": false
    },
    {
      "id": 4,
      "code": "WELCOME35",
      "type": "percentage",
      "value": "10.00",
      "max_uses": null,
      "per_user_limit": null,
      "first_time_only": 0,
      "active_from": "2025-01-01T00:00:00.000000Z",
      "active_to": "2025-12-31T23:59:59.000000Z",
      "min_cart_total": "50.00",
      "applicable_products": [
        2,
        6
      ],
      "applicable_categories": [
        3,
        7
      ],
      "expires_at": null,
      "created_at": "2025-02-24T12:08:36.000000Z",
      "updated_at": "2025-02-24T12:08:36.000000Z",
      "stackable": true
    },
    {
      "id": 5,
      "code": "WELCOME30",
      "type": "percentage",
      "value": "10.00",
      "max_uses": null,
      "per_user_limit": null,
      "first_time_only": 0,
      "active_from": "2025-01-01T00:00:00.000000Z",
      "active_to": "2025-12-31T23:59:59.000000Z",
      "min_cart_total": "50.00",
      "applicable_products": [
        2,
        6
      ],
      "applicable_categories": [
        3,
        7
      ],
      "expires_at": null,
      "created_at": "2025-02-24T13:21:03.000000Z",
      "updated_at": "2025-02-24T13:21:03.000000Z",
      "stackable": true
    }
  ]
}

POST /api/discounts/store  End point

### Json format request body ###

{
  "code": "WELCOME30",
  "type": "percentage",
  "value": "10",
  "min_cart_total": "50",
  "applicable_products": [
    2,
    6
  ],
  "applicable_categories": [
    3,
    7
  ],
  "active_from": "2025-01-01 00:00:00",
  "active_to": "2025-12-31 23:59:59",
  "stackable": true
}

### Reponse in json body ###

status code 201
{
  "code": "WELCOME10",
  "type": "fixed or percentage",
  "value": "10",
  "min_cart_total": "50",
  "applicable_products": [
    2,
    6
  ],
  "applicable_categories": [
    3,
    7
  ],
  "max_uses": 100,
  "per_user_limit": 3,
  "first_time_only": true,
  "active_from": "2025-01-01 00:00:00",
  "active_to": "2025-12-31 23:59:59"
}

### Error response ###

status code 422
{
  "message": "The code has already been taken. (and 1 more error)",
  "errors": {
    "code": [
      "The code has already been taken."
    ],
    "value": [
      "The value field is required."
    ]
  }
}



POST /api/discounts/apply  End Point

### Json format request body ###

{
  "cart_total": "100",
  "cart_items": [
    {
      "product_id": 1,
      "category_id": 5,
      "price": 50
    },
    {
      "product_id": 2,
      "category_id": 5,
      "price": 50
    }
  ],
  "discount_codes": [
    "WELCOME10"
  ]
}


### Reponse in json body ###

Status code 200

{
  "discount_applied": 10,
  "final_total": 90,
  "applied_discounts": [
    "WELCOME10"
  ]
}

### Error Response ###

Status Code 422
{
  "message": "The cart total field is required.",
  "errors": {
    "cart_total": [
      "The cart total field is required."
    ]
  }
}

```


## Unit Test

- PASS  Tests\Unit\DiscountServiceTest
- discount applies correctly                                                                                   1.16s<br>
- zero discount returns full price                                                                             0.03s<br>
- full discount returns zero                                                                                   0.03s<br>
- discount does not apply if below min cart total                                                              0.03s<br>
- discount does not apply if expired                                                                           0.03s


## Feature OR DB Test

- PASS Tests\Feature\DiscountApiTest <br>
- is discount model exist 1.54s <br>
- can create a discount 0.32s <br>
- apply discount to cart 0.03s <br>
- discount does not apply if invalid code 0.03s <br>
- discount does not apply if cart total below minimum 0.03s <br>
- get discounts list success 0.04s <br>
- non stackable discount blocks others 0.03s <br>
- stackable discount apply 0.03s

- Tests: 13 passed (24 assertions) Duration: 4.92s

To run tests, run the following command

```
  php artisan test
```
## Feedback

If you have any feedback, please reach out to me at 88khanm@fake.com

