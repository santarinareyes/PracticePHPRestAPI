# E-commerce REST API

<!-- TOC depthFrom:1 depthTo:2 withLinks:1 updateOnSave:1 orderedList:0 -->

- [E-commerce REST API](#e-commerce-rest-api)
    - [Preamble](#preamble)
    - [API overview](#api-overview)
    - [Endpoints](#endpoints)
    - [How to](#how-to)
    - [Success response examples](#success-response-examples)
    - [Error response examples](#error-response-examples)

<!-- /TOC -->



## Preamble
This is a school assignment. The project is about creating a REST API for an E-commerce. Authentication token method is used and we use both refresh token and access token.
Refresh token will expire after 30 days and the access token will expire after 30 minutes. Both refresh and access token will update on a session update.


## API overview
The API is RESTFUL and returns results in JSON. 
The features are simple and we follow the CRUD principles.


    {
      "statusCode": 307,
      "success": true,
      "messages": [
          "REST-API by: Richard Santarina Reyes"
      ],
      "data": {
          "socials": {
              "Github": "https://github.com/santarinareyes",
              "Contact": "rsrprivat@gmail.com"
          }
      }
    }

## Endpoints 
[![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) [![Generic badge](https://img.shields.io/badge/User-<COLOR>.svg)](https://shields.io/)

### Users
| Method     | URL | Description |
|:-----------|:----------------|:-----------|
| `POST` | /users/ | [![Generic badge](https://img.shields.io/badge/User-<COLOR>.svg)](https://shields.io/) - [create user](#how-to) |
| `GET` | /users/ | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) - show all users |
| `GET` | /users/page/1 | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) - show 20 users per page |
| `GET` | /users/admin | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) - show all admins |
| `GET` | /users/user | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) - show all users |
| `PATCH` | /users/1 | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) [![Generic badge](https://img.shields.io/badge/User-<COLOR>.svg)](https://shields.io/) - update user id 1 - Admin can change any user info. Users can only change their own info |
| `DELETE` | /users/1 | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) [![Generic badge](https://img.shields.io/badge/User-<COLOR>.svg)](https://shields.io/) - update user id 1 - Admin can delete any user. Users can only delete their own account |

### Sessions
| Method     | URL | Description |
|:-----------|:----------------|:-----------|
| `POST` | /sessions/ | [![Generic badge](https://img.shields.io/badge/User-<COLOR>.svg)](https://shields.io/) - [create session/log in]#how-to) |
| `GET` | /sessions/ | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) - show all sessions |
| `GET` | /sessions/page/1 | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) - show 20 sessions per page |
| `GET` | /sessions/1 | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) - show all sessions for user id 1 |
| `GET` | /sessions/username | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) - show all sessions for username |
| `PATCH` | /sessions/1 | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) [![Generic badge](https://img.shields.io/badge/User-<COLOR>.svg)](https://shields.io/) - update session id 1 - Admin can update any sess. Users can only update their own sess |
| `DELETE` | /sessions/1 | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) [![Generic badge](https://img.shields.io/badge/User-<COLOR>.svg)](https://shields.io/) - delete session id 1 - Admin can delete any sess. Users can only delete their own sess |

### Categories
| Method     | URL | Description |
|:-----------|:----------------|:-----------|
| `POST` | /categories/ | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) - [create category](#how-to) |
| `GET` | /categories/ | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) [![Generic badge](https://img.shields.io/badge/User-<COLOR>.svg)](https://shields.io/) - show all categories |
| `GET` | /categories/page/1 | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) [![Generic badge](https://img.shields.io/badge/User-<COLOR>.svg)](https://shields.io/) - show 20 categories per page |
| `PATCH` | /categories/1 | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) - update category id 1 |
| `DELETE` | /categories/1 | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) - delete category id 1 |

### Products
| Method     | URL | Description |
|:-----------|:----------------|:-----------|
| `POST` | /products/ | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) - [create product](#how-to) |
| `GET` | /products/ | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) [![Generic badge](https://img.shields.io/badge/User-<COLOR>.svg)](https://shields.io/) - show all products |
| `GET` | /products/page/1 | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) [![Generic badge](https://img.shields.io/badge/User-<COLOR>.svg)](https://shields.io/) - show 20 products per page |
| `PATCH` | /products/1 | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) - update product id 1 |
| `DELETE` | /products/1 | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) - delete product id 1 |

### Shopping cart
| Method     | URL | Description |
|:-----------|:----------------|:-----------|
| `POST` | /carts/ | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) [![Generic badge](https://img.shields.io/badge/User-<COLOR>.svg)](https://shields.io/) - [add product to cart](#how-to) |
| `GET` | /carts/ | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) - show all active carts |
| `GET` | /carts/page/1 | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) - show 20 active carts per page |
| `GET` | /carts/total | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) - show all active carts with total price of products in cart |
| `GET` | /carts/user | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) - show a users added products in carts |
| `DELETE` | /carts/1 | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) [![Generic badge](https://img.shields.io/badge/User-<COLOR>.svg)](https://shields.io/) - delete cart(item) id 1 - Users can delete products from their own cart |
| `DELETE` | /carts/username | [![made-with-python](https://img.shields.io/badge/Admin-1f425f.svg)](#) [![Generic badge](https://img.shields.io/badge/User-<COLOR>.svg)](https://shields.io/) - delete/username - Admin can delete a users cart. A user can delete their own |

## How to
<details>
<summary>Create users/sessions/categories/products/carts?</summary>

### Create user
    {
    "firstname":"example",
    "lastname":"example",
    "username":"example",
    "email":"example@example.com",
    "password":"example"
    }
    
 ### Create Session/Log in
    {
    "username":"example",
    "password":"example"
    }
    
 ### Create category
    {
    "title":"example"
    }
    
 ### Create product
    {
    "title":"example",
    "category":"example",
    "price":"100.00",
    "description":"example",
    }
    
 ### Add product to cart
    {
    "username":"example",
    "product":"example"
    }


</details>
    
## Success response examples
<details>
<summary>Show me</summary>

### User created success
    {
        "statusCode": 201,
        "success": true,
        "messages": [
            "User created"
        ],
        "data": {
            "rows_returned": 1,
            "users": [
                {
                    "id": "28",
                    "firstname": "Example",
                    "lastname": "Example",
                    "username": "example",
                    "email": "example@example.com",
                    "role": "User"
                }
            ]
        }
    }

### Session created success
      {
          "statusCode": 201,
          "success": true,
          "messages": [],
          "data": {
              "rows_returned": 1,
              "sessions": {
                  "session_id": "27",
                  "username": "example",
                  "email": "example@example.com",
                  "accesstoken": "OTcxZDlkMDRjNjBhZTI1ZTZkOTY1NDg5ZDMxNTcyOWY2ZDA4MDhhYjExNTUxN2Q1MTYxNjQ0OTUwMw==",
                  "accesstoken_expiry": "30 min",
                  "refreshtoken": "NjM2NTIyOGU0OWNkMzhmZDE5MWU0ZTFhNDc5ZDZkNDQ2YjVlNzBhMTJiYmFjZjhhMTYxNjQ0OTUwMw==",
                  "refreshtoken_expiry": "30 days"
              }
          }
      }

### Fetch carts as admin
      {
          "statusCode": 200,
          "success": true,
          "messages": [],
          "data": {
              "rows_returned": 2,
              "carts": [
                  {
                      "user_id": "1",
                      "username": "santa",
                      "priceTotal": "268884.00"
                  },
                  {
                      "user_id": "27",
                      "username": "santarinareyes",
                      "priceTotal": "12222.00"
                  }
              ]
          }
      }
      
</details>

## Error response examples
<details>
<summary>Show me</summary>

## User exist error
      {
          "statusCode": 409,
          "success": false,
          "messages": [
              [
                  "Email already exist",
                  "Username already exist"
              ]
          ],
          "data": null
      }
      
## Invalid access token error

    {
        "statusCode": 401,
        "success": false,
        "messages": [
            [
                "Invalid Access Token"
            ]
        ],
        "data": null
    }
    
## User does not match error

      {
          "statusCode": 400,
          "success": false,
          "messages": [
              [
                  "User Id does not match the logged in user Id. Please try again."
              ]
          ],
          "data": null
      }
      
## Access token missing error

      {
          "statusCode": 401,
          "success": false,
          "messages": [
              [
                  "Access token is missing from the header",
                  "Access token cannot be blank"
              ]
          ],
          "data": null
      }

</details>
