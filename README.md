# Azure AD B2C single sign-on for Laravel single-page applications

## Installation
**Install the package:**  
`> composer require wika-group/azureadb2c-spa-for-laravel`

**Publish assets:**  
`> php artisan vendor:publish --tag=azureb2cspa-assets`

**Extend .env and configure:**  
```ini
AADB2C_CLIENT_ID=
AADB2C_DOMAIN=            # {your_domain}.b2clogin.com
AADB2C_CUSTOM_DOMAIN=     # Optional: set to use custom domain e.g. login.contoso.com
AADB2C_POLICY=            # Optional - Default: 'B2C_1_sign-up_and_sign-in_policy'
AADB2C_DEFAULT_ALGORITHM= # Optional: Decoding algorithm JWK key. Default: 'RS256'
```

## Usage
### Add scripts to your views
```
@azureB2cSpaScripts()
```

If you use it with Livewire or Wire Extender, you must add `@assets`:
```
@assets
@azureB2cSpaScripts()
@endassets
```

### Add a button to trigger login
```HTML
<button onClick="b2cPopupLogin()">Login with Azure B2C</button>
```
