---
layout: home

hero:
  name: "Basalam Laravel"
  text: "Seamless Basalam API integration for modern Laravel apps"
  tagline: "Zero external SDK dependencies. Full interface contracts. Laravel 11, 12, 13+ support."
  actions:
    - theme: brand
      text: Get Started
      link: /guide/getting-started
    - theme: alt
      text: Explore Contracts & DTOs
      link: /guide/architecture-contracts

features:
  - title: Pure Laravel HTTP Engine
    details: Built directly upon Laravel's native HTTP client (`Http::fake()`, retries, pools) with zero third-party Basalam SDK dependencies.
  - title: Interface Contracts Architecture
    details: Bind your existing Eloquent models (Product, Cart, Order) straight into the package via standard contracts. No manual translation layers.
  - title: Multi-Version Laravel Ready
    details: Engineered and tested on PHP 8.2, 8.3, 8.4 across Laravel 11, 12, and upcoming Laravel 13+.
  - title: Gateway & Core Unified
    details: Seamlessly coordinates between Basalam Gateway (`openapi.basalam.com`), Core (`core.basalam.com`), and Auth (`auth.basalam.com`).
  - title: OAuth 2.0 & Personal Tokens
    details: Supports Personal Access Tokens for internal shop tools, Authorization Code flow for public apps, and Client Credentials.
  - title: 100% TDD Tested
    details: Comprehensive test suite covering authentication, products, carts, orders, parcel logistics, webhooks, and file uploads.
---
