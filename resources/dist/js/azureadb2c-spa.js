const loginRequest = { scopes: ["openid"] };
let myMSALObj = undefined

function initMSAL(msalConfig) {
    window.b2cLoginApiUrl = msalConfig.b2cLoginApiUrl;
    window.b2cLogoutApiUrl = msalConfig.b2cLogoutApiUrl;
    window.msalConfigIsAlreadyLoggedIn = msalConfig.msalConfigIsAlreadyLoggedIn;

    // Create the main myMSALObj instance
    // configuration parameters are located at scripts.blade.php
    myMSALObj = new msal.PublicClientApplication(msalConfig);
    myMSALObj.initialize().then(() => {
        myMSALObj.handleRedirectPromise().then(handleMsalInitResponse).catch(err => {
            console.error(err);
        });
    });
}

async function handleMsalInitResponse(resp) {
    if (window.msalConfigIsAlreadyLoggedIn) {
        return;
    }

    handleMsalResponse(resp);
}

async function handleMsalResponse(resp) {
    if (resp !== null) {
        myMSALObj.setActiveAccount(resp.account);
        window.Livewire.dispatch("azureb2c-login", {token: resp.account.idToken});
        return;
    }

    const currentAccounts = myMSALObj.getAllAccounts();

    if (!currentAccounts || currentAccounts.length < 1) {
        window.Livewire.dispatch("azureb2c-login-failed", { msg: "No account was found" });
        return;
    }

    if (currentAccounts.length > 1) {
        window.Livewire.dispatch("azureb2c-login-failed", { msg: "Cannot handle multiple accounts" });
        return;
    }

    const activeAccount = currentAccounts[0];
    myMSALObj.setActiveAccount(activeAccount);
    window.Livewire.dispatch("azureb2c-login", { token: activeAccount.idToken });
}

async function b2cPopupLogin() {
    return myMSALObj.loginPopup({
        ...loginRequest, redirectUri: window.location.href
    }).then(handleMsalResponse).catch(function (error) {
        console.error(error);
    });
}

async function b2cLogout() {
    window.Livewire.dispatch("azureb2c-logout");
}