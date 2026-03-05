// interface_frontend/ressources/js/api.js

const API_BASE_URL = '../../../api';

async function apiCall(endpoint, method = 'GET', body = null) {
    const headers = {
        'Content-Type': 'application/json'
    };

    const options = {
        method,
        headers
    };

    if (body) {
        options.body = JSON.stringify(body);
    }

    try {
        const response = await fetch(`${API_BASE_URL}/${endpoint}`, options);
        // Ensure successful response OR parse json for error msg
        let data;
        try {
            data = await response.json();
        } catch (e) {
            throw new Error('Erreur de parsing de la réponse serveur.');
        }

        if (!response.ok) {
            throw new Error(data.message || 'Erreur API');
        }

        return data;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}
