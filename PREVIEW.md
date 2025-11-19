# Abandoned Arcade - Platform Overview

## Mobile-First API Architecture

Abandoned Arcade is built with a robust RESTful API architecture designed to power mobile applications across iOS and Android platforms. The backend services are optimized for performance, scalability, and security to ensure smooth integration with mobile development teams.

### API Authentication & Security

- **Token-Based Authentication**: Laravel Sanctum provides secure API token management for mobile apps
- **Username-Based Login**: Users authenticate using unique usernames (no email required)
- **HTTPS-Ready**: All API endpoints support secure connections with SSL/TLS encryption
- **Password Security**: All passwords are hashed using bcrypt with secure validation
- **CORS Support**: Configured for cross-origin requests from mobile applications

### RESTful API Endpoints

The platform provides comprehensive JSON API endpoints for seamless mobile integration:

#### Public Endpoints (No Authentication Required)
- `GET /api/games` - Browse game collection with filtering and pagination
- `GET /api/games/autocomplete` - Real-time search autocomplete
- `GET /api/games/{id}` - Get detailed game information
- `GET /api/reviews` - Browse game reviews
- `GET /api/reviews/{id}` - Get specific review details

#### Authentication Endpoints
- `POST /api/register` - Create new user account (returns auth token)
- `POST /api/login` - Authenticate user (returns auth token)

#### Protected Endpoints (Require Bearer Token)
- `GET /api/user` - Get current authenticated user profile
- `POST /api/logout` - Revoke authentication token
- `POST /api/guest/sync` - Sync guest localStorage data to account

#### Game Management
- `GET /api/games` - List games (with filtering: genre, featured, search)
- `GET /api/games/{id}` - Get game details with reviews
- `POST /api/games` - Create new game entry (authenticated)
- `PUT /api/games/{id}` - Update game information (authenticated)
- `DELETE /api/games/{id}` - Remove game (authenticated)

#### Reviews & Ratings
- `GET /api/reviews` - List all reviews
- `GET /api/reviews/{id}` - Get review details
- `POST /api/reviews` - Create review (authenticated)
- `PUT /api/reviews/{id}` - Update review (authenticated)
- `DELETE /api/reviews/{id}` - Delete review (authenticated)

#### Save States
- `GET /api/saves` - List user's save states (with optional game filter)
- `POST /api/saves` - Create new save state
- `GET /api/saves/{id}` - Get save state with full game data
- `PUT /api/saves/{id}` - Update save state
- `DELETE /api/saves/{id}` - Delete save state

### Database Design & Optimization

- **PostgreSQL Database**: Serverless Neon PostgreSQL for scalable data storage
- **Optimized Queries**: Eager loading relationships to minimize database queries
- **Efficient Relationships**: Well-designed foreign keys and indexes
- **Schema Migrations**: Version-controlled database structure with Laravel migrations

### Mobile Development Integration

The API is specifically designed for mobile workflows:

- **JSON-Only Responses**: All endpoints return standardized JSON format
- **Pagination Support**: Large datasets are paginated for efficient mobile data loading
- **Query Filtering**: Support for filtering, searching, and sorting via query parameters
- **Guest Mode Sync**: Allows users to start as guests and seamlessly sync data upon registration
- **Offline Support**: Guest mode uses localStorage, with sync capability when online

### API Response Format

All API responses follow a consistent JSON structure:

```json
{
  "data": [...],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100
  }
}
```

### Testing & Documentation

- **API Test Script**: PowerShell script for quick endpoint testing (`test-api.ps1`)
- **Error Handling**: Comprehensive validation and error messages
- **Response Resources**: Laravel API Resources ensure consistent response formatting

### Third-Party Integrations

- **WorkOS**: Optional enterprise authentication and user management
- **Laravel Sanctum**: API token authentication system
- **CORS Middleware**: Cross-origin resource sharing for mobile apps

## Platform Features

### User Experience
- **Guest Mode**: Play games without account creation
- **Save States**: Cloud storage for game progress
- **Leaderboards**: High score tracking and competition
- **Reviews & Ratings**: Community-driven game recommendations
- **Search & Discovery**: Advanced filtering by genre, featured status, and search terms

### Technical Capabilities
- **Performance Optimization**: Efficient database queries and caching
- **Scalability**: Designed to handle growing user base and game library
- **Security**: Industry-standard authentication and data protection
- **Monitoring**: Built-in logging and debugging capabilities
- **Deployment**: Ready for cloud deployment (Docker, Railway, Render support)

## Mobile App Support

The API architecture supports native mobile applications with:

- **Token-Based Sessions**: Long-lived authentication tokens for mobile apps
- **Data Synchronization**: Sync guest data when users create accounts
- **Offline-First Approach**: Guest mode works offline, syncs when connected
- **Optimized Payloads**: Efficient JSON responses for mobile data consumption
- **Error Handling**: Clear error messages for mobile app integration

