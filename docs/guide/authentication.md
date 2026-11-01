# Authentication & OAuth 2.0

Basalam supports three primary authentication schemes:
1. **Personal Access Token (PAT)**: For internal shop utilities or single-vendor syncs.
2. **OAuth 2.0 Authorization Code Flow**: For public apps, multi-tenant SaaS platforms, or app store extensions.
3. **Client Credentials Flow**: For system-to-system services (e.g., Wallet APIs).

---

## 1. Personal Access Token (PAT)

Generate a token in the [Basalam Developer Panel](https://developers.basalam.com/panel/tokens), selecting your required scopes.

Define it in `.env`:
```env
BASALAM_TOKEN=eyJ0eXAiOi...
```

All requests through `Basalam` facade will automatically use this Bearer token.

---

## 2. OAuth 2.0 Authorization Code Flow

### Step 1: Redirect User to Basalam SSO

```php
use Eamirgh\Basalam\Facades\Basalam;
use Illuminate\Http\Request;

public function redirectToBasalam(Request $request)
{
    $state = bin2hex(random_bytes(16));
    $request->session()->put('basalam_oauth_state', $state);

    $url = Basalam::auth()->getAuthorizationUrl(
        redirectUri: route('basalam.callback'),
        scopes: [
            'vendor.product.read',
            'vendor.product.write',
            'customer.order.read',
        ],
        state: $state
    );

    return redirect()->away($url);
}
```

### Step 2: Handle OAuth Callback & Exchange Code

```php
use Eamirgh\Basalam\Facades\Basalam;
use Illuminate\Http\Request;

public function handleCallback(Request $request)
{
    $savedState = $request->session()->pull('basalam_oauth_state');
    if (!$savedState || $savedState !== $request->query('state')) {
        abort(403, 'Invalid OAuth state.');
    }

    $code = $request->query('code');

    // Exchange authorization code for TokenData DTO
    $token = Basalam::auth()->exchangeCode(
        code: $code,
        redirectUri: route('basalam.callback')
    );

    // Save tokens in your database associated with user
    auth()->user()->update([
        'basalam_access_token' => $token->accessToken,
        'basalam_refresh_token' => $token->refreshToken,
        'basalam_token_expires_at' => now()->addSeconds($token->expiresIn),
    ]);

    return redirect()->route('dashboard')->with('success', 'Connected to Basalam!');
}
```

### Step 3: Refreshing Tokens

```php
$newToken = Basalam::auth()->refreshToken($user->basalam_refresh_token);

$user->update([
    'basalam_access_token' => $newToken->accessToken,
    'basalam_refresh_token' => $newToken->refreshToken,
]);
```

---

## 3. Dynamic Multi-Tenant Execution (`withToken`)

In a multi-tenant or multi-user application, switch the active token dynamically per request without affecting the global configuration:

```php
use Eamirgh\Basalam\Facades\Basalam;

// Execute scoped to a specific vendor user's token:
$userToken = auth()->user()->basalam_access_token;

$vendorProducts = Basalam::withToken($userToken)
    ->products()
    ->listByVendor($vendorId);
```

You can also pass a closure for lazy resolution or automatic token refreshing:

```php
$scoped = Basalam::withToken(function () use ($user) {
    if ($user->isBasalamTokenExpired()) {
        $refreshed = Basalam::auth()->refreshToken($user->basalam_refresh_token);
        $user->update([
            'basalam_access_token' => $refreshed->accessToken,
            'basalam_refresh_token' => $refreshed->refreshToken,
        ]);
        return $refreshed->accessToken;
    }

    return $user->basalam_access_token;
});

$products = $scoped->products()->list();
```

---

## 4. Client Credentials Grant

For server-to-server operations requiring client credentials:

```php
$token = Basalam::auth()->clientCredentials(scopes: ['wallet.read', 'wallet.write']);

echo $token->accessToken;
```
