<div>
    @assets
    <script src="{{ url('vendor/azureb2cspa/js/msal-browser.min.js') }}"></script>
    <script src="{{ url('vendor/azureb2cspa/js/azureadb2c-spa.js') }}"></script>
    @endassets

    @script
    <script>
        // Src: https://github.com/AzureAD/microsoft-authentication-library-for-js/tree/dev/samples/msal-browser-samples/VanillaJSTestApp2.0/app/default
        // Config object to be passed to Msal on creation
        initMSAL({
            b2cLoginApiUrl: "{{ url('azureb2c/login') }}",
            b2cLogoutApiUrl: "{{ url('azureb2c/logout') }}",
            msalConfigIsAlreadyLoggedIn: "{{ auth()->guest() ? 'false' : 'true' }}",
            auth: {
                clientId: "{{ config('azureadb2c.connection.client_id') }}",
                authority: "https://{{ config('azureadb2c.connection.custom_domain') ?? config('azureadb2c.connection.domain') . '.b2clogin.com' }}/{{ config('azureadb2c.connection.domain') }}.onmicrosoft.com/{{ config('azureadb2c.connection.policy') }}",
                knownAuthorities: ["{{ config('azureadb2c.connection.custom_domain') ?? config('azureadb2c.connection.domain') . '.b2clogin.com' }}"],
            },
            cache: {
                cacheLocation: "sessionStorage",
                storeAuthStateInCookie: false,
            },
            system: {
                loggerOptions: {
                    logLevel: msal.LogLevel.Trace,
                    loggerCallback: (level, message, containsPii) => {
                        if (containsPii) {
                            return;
                        }
                        if (level === msal.LogLevel.Error || level === msal.LogLevel.Warning) {
                            console.error(message);
                        }
                    },
                },
            },
        });
    </script>
    @endscript
</div>