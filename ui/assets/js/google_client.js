// var client;
// const authorizedUri = "http://localhost/29k-redevelopment/register";
// function initClient() {
//     client = google.accounts.oauth2.initTokenClient({
//         client_id: '',
//         scope: 'https://www.googleapis.com/auth/calendar.readonly \
//                 https://www.googleapis.com/auth/documents.readonly \
//                 https://www.googleapis.com/auth/photoslibrary.readonly',
//         callback: (response) => {
//             console.log(response);
//             const accessToken = response.access_token; // Replace this with the actual access token
//             fetch("https://www.googleapis.com/oauth2/v3/userinfo", {
//             method: "GET",
//             headers: {
//                 Authorization: `Bearer ${accessToken}`,
//             },
//             })
//             .then((response) => response.json())
//             .then((data) => {
//                 // Handle the user information in the 'data' object
//                 console.log("User information:", data);
//                 // Extract the username from the 'data' object and use it as needed
//                 const username = data.name;
//                 console.log("Username:", username);
//             })
//             .catch((error) => {
//                 // Handle errors if any
//                 console.error("Error fetching user information:", error);
//             });
//         }
//     });
// }
// function getAuthCode() {
//     client.requestAccessToken();
// }

// // this is code for when we dont wanna use the login button

// window.fbAsyncInit = function() {
//     function testAPI() { // Testing Graph API after login.  See statusChangeCallback() for when this call is made.
//         console.log('Welcome!  Fetching your information.... ');
//         FB.api('/me', function(response) {
//             console.log('Successful login for: ' + response.name);
//         });
//     }
//     FB.init({
//         appId      : '',
//         cookie     : true,
//         xfbml      : true,
//         version    : 'v3.2'
//     });
//     FB.AppEvents.logPageView();   
//     function statusChangeCallback(response) {  // Called with the results from FB.getLoginStatus().
//         if (response.status === 'connected') { 
//             testAPI();
//             console.log('Logged in.');  // Logged into your webpage and Facebook.
//         } else {                                 // Not logged into your webpage or we are unable to tell.
//           console.log('Not logged in.');
//         }
//     }
//     FB.getLoginStatus(function(response) {
//         statusChangeCallback(response);
//     });
// };

// (function(d, s, id){
//     var js, fjs = d.getElementsByTagName(s)[0];
//     if (d.getElementById(id)) {return;}
//     js = d.createElement(s); js.id = id;
//     js.src = "https://connect.facebook.net/en_US/sdk.js";
//     fjs.parentNode.insertBefore(js, fjs);
// }(document, 'script', 'facebook-jssdk'));
