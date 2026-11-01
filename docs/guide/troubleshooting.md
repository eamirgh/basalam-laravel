# Troubleshooting & FAQ

## Common API Errors

| Error Code / Status | Cause | Solution |
|---|---|---|
| `401 Unauthorized` | Missing, malformed, or expired token | Check `Authorization: Bearer <token>` header, or refresh token via `Basalam::auth()->refreshToken(...)`. |
| `403 Forbidden` | The token does not have the required OAuth scope | Go to Basalam Developer Panel or verify scopes requested in `getAuthorizationUrl()`. |
| `422 Unprocessable Entity` | Required field missing or validation failed | Inspect `$exception->getErrors()` or `$exception->getResponseBody()`. |
| `invalid_client` | Client ID or Client Secret incorrect | Verify `BASALAM_CLIENT_ID` and `BASALAM_CLIENT_SECRET` in `.env`. |
| `redirect_uri_mismatch` | Callback URL does not match developer panel settings | Protocol, host, port, and trailing slash must match character-by-character. |
| `invalid_grant` | Auth code expired or reused | Auth codes are one-time use; initiate authorization flow again. |

## Network Timeouts & Retries

If calls to Basalam's servers occasionally encounter slow response times or transient network drops, configure automatic retries in `config/basalam.php`:

```php
'timeout' => 45, // seconds
'retry' => [
    'times' => 3,   // retry up to 3 times
    'sleep' => 200, // sleep 200ms between attempts
],
```

## Why are there separate URLs for Gateway and Core?

Basalam uses a microservice architecture:
- `https://openapi.basalam.com`: High-throughput Gateway for public API endpoints (Products, Orders, Parcels, Files, Baskets).
- `https://core.basalam.com`: Core vendor settings, shipping profiles, bank info, and category taxonomies.
- `https://auth.basalam.com`: OAuth token issuance, refresh, and whoami inspection.
- `https://basalam.com/accounts/sso`: User login & authorization screen.

The package handles all URL routing transparently.
