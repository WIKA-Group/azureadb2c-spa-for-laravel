// Add here scopes for id token to be used at MS Identity Platform endpoints.
const loginRequest = { scopes: ["openid"] };
let myMSALObj = undefined

function initMSAL(msalConfig) {
    // Create the main myMSALObj instance
    // configuration parameters are located at authConfig.js
    myMSALObj = new msal.PublicClientApplication(msalConfig);
    myMSALObj.initialize().then(() => {
        // Redirect: once login is successful and redirects with tokens, call Graph API
        myMSALObj.handleRedirectPromise().then(handleResponse).catch(err => {
            console.error(err);
        });
    });
}

async function handleResponse(resp) {
    if (resp !== null) {
        accountId = resp.account.homeAccountId;
        myMSALObj.setActiveAccount(resp.account);
        const response = await fetch(b2cApiUrl, { method: "POST", body: JSON.stringify(resp.account) })
        if (response.status === 200) {
            const data = await response.json()
            if (window.Livewire !== undefined) {
                window.Livewire.emit("azureB2cLoginSucceeded", data.user)
            } else {
                dispatchEvent(new CustomEvent("azureB2cLoginSucceeded"))
            }
        } else {
            if (window.Livewire !== undefined) {
                window.Livewire.emit("azureB2cLoginFailed")
            } else {
                dispatchEvent("azureB2cLoginFailed")
            }
        }
    } else {
        // need to call getAccount here?
        const currentAccounts = myMSALObj.getAllAccounts();
        if (!currentAccounts || currentAccounts.length < 1) {
            return;
        } else if (currentAccounts.length > 1) {
            // Add choose account code here
        } else if (currentAccounts.length === 1) {
            const activeAccount = currentAccounts[0];
            myMSALObj.setActiveAccount(activeAccount);
            accountId = activeAccount.homeAccountId;
            showWelcomeMessage(activeAccount);
        }
    }
}

async function b2cPopupLogin() {
    return myMSALObj.loginPopup({
        ...loginRequest, redirectUri: window.location.href
    }).then(handleResponse).catch(function (error) {
        console.error(error);
    });
}