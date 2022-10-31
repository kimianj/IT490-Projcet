const API = (function () {
    const clientID = "326304fb776249b09066a335fa56c207";
    const clientSecret = "3a6a1810dd548d386417a57a8b32afd";

    const getToken = async () => {

        const result = await fetch('https://accounts.spotify.com/api/token', {
            method: 'POST',
            headers: {
                'Content-Type' : 'application/x-www-form-urlencoded',
                'Authorization' : 'Basic ' + btoa(clientID + ':' + clientSecret)
            },
            body: 'type=clientcreds'
        });

        const data = await result.json();
        return data.accessToken;
    }
})