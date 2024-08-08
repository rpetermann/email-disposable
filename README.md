# Disposable Email Checker

This project is a personal initiative to create a disposable (temporary) email checker using [Hyperf](https://github.com/hyperf/hyperf), a high-performance framework built on top of [PHP Swoole](https://github.com/swoole/swoole-src).

## Table of Contents
- [Introduction](#introduction)
- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)

## Introduction
Disposable email addresses are temporary email addresses that expire after a certain period or after a single use. This project aims to identify and filter out such email addresses to ensure the integrity of user data.

## Features
- **High Performance**: Built using Hyperf and PHP Swoole for asynchronous and high-performance operations.
- **Email Validation**: Checks if an email address is from a known disposable email provider.
- **Extensible**: Easily add new disposable email providers to the list.
- **API Support**: Provides a RESTful API for integration with other services.

## Installation
To install and set up the project using Docker, follow these steps:

1. **Clone the repository**:
    ```sh
    git clone https://github.com/yourusername/disposable-email-checker.git
    cd disposable-email-checker
    ```

2. **Set up environment variables**:
    Copy the `.env.example` file to `.env` and configure your environment variables.
    ```sh
    cp .env.example .env
    ```

3. **Build and Run using Docker**:
    ```sh
    docker-compose up --build
    ```

## Usage
To use the disposable email checker, you can send a GET request to the API endpoint with the email address or domain you want to check.

Example:
```sh
curl --location 'http://127.0.0.1:9501/v1/email-disposable/check?evaluator=allOrNothing&emailOrDomain=disposable.com'
