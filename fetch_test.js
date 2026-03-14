const https = require('https');

const options = {
    hostname: 'eco-ride-nu.vercel.app',
    port: 443,
    path: '/api/seed_vercel.php',
    method: 'GET',
    headers: {
        'Accept': 'application/json',
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36'
    }
};

const req = https.request(options, res => {
    console.log(`statusCode: ${res.statusCode}`);
    let data = '';
    res.on('data', d => data += d);
    res.on('end', () => console.log(data));
});

req.on('error', error => console.error(error));
req.end();
