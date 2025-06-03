# Azure AD B2C single sign-on for Laravel single-page applications

## Installation
**Install the package:**
```bash
composer require wika-group/azureadb2c-spa-for-laravel
```

**Publish assets:**
```bash
php artisan vendor:publish --tag=azureb2cspa-assets
```

**Publish migration:**
```bash
php artisan vendor:publish --tag=azureb2cspa-migrations
```

**Extend .env and configure:**
```ini
AADB2C_CLIENT_ID=
AADB2C_DOMAIN=            # {your_domain}.b2clogin.com
AADB2C_CUSTOM_DOMAIN=     # Optional: set to use custom domain e.g. login.contoso.com
AADB2C_POLICY=            # Optional - Default: 'B2C_1_sign-up_and_sign-in_policy'
AADB2C_DEFAULT_ALGORITHM= # Optional: Decoding algorithm JWK key. Default: 'RS256'
AADB2C_OAUTH_COLUMN=      # Optional: Name of the OAuth ID column. Default 'oauth_id'
```

**Optional: Publish config:**
```bash
php artisan vendor:publish --tag=azureb2cspa-config
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

If you use Wire Extender, you must [Enable browser session support](https://wire-elements.dev/blog/embed-livewire-components-using-wire-extender).

### Add a button to trigger login
```HTML
<button onClick="b2cPopupLogin()">Login with Azure B2C</button>
```

### Hook into the login event
You can add custom logic by using the emitted events:
For succeeded login the following events are emitted:
```js
if (window.Livewire !== undefined) {
    window.Livewire.emit("azureB2cLoginSucceeded", data.user)
} else {
    dispatchEvent(new CustomEvent("azureB2cLoginSucceeded"))
}
```

For a failed login the following events are emitted:
```js
if (window.Livewire !== undefined) {
    window.Livewire.emit("azureB2cLoginFailed")
} else {
    dispatchEvent("azureB2cLoginFailed")
}
```

### Configure Azure B2C
You must add the URL of the SPA in the Azure Portal:

<img src="https://github.com/WIKA-Group/azureadb2c-spa-for-laravel/blob/main/img/AzurePortalAppRegistration.jpg" width="700">
