// translate.js

const subscriptionKey = "FhIEJd2BTrDXCjNfnugU5aLIQIZGKZa3OXPMvVwquGJjoH8bfqqSJQQJ99BEACLArgHXJ3w3AAAbACOGrmSy"; // Tu clave de API
const endpoint = "https://api.cognitive.microsofttranslator.com/";
const region = "southcentralus"; // Región de tu servicio

async function translateText(text, toLang = "en") {
    const url = `${endpoint}/translate?api-version=3.0&to=${toLang}`;
    const headers = {
        "Ocp-Apim-Subscription-Key": subscriptionKey,
        "Ocp-Apim-Subscription-Region": region,
        "Content-Type": "application/json"
    };

    const body = JSON.stringify([{ Text: text }]);

    const response = await fetch(url, {
        method: "POST",
        headers: headers,
        body: body
    });

    const data = await response.json();
    return data[0].translations[0].text;
}

async function translateTextNodes(node, lang) {
    const childNodes = Array.from(node.childNodes);

    for (const child of childNodes) {
        if (child.nodeType === Node.TEXT_NODE && child.nodeValue.trim().length > 0) {
            try {
                const translated = await translateText(child.nodeValue, lang);
                child.nodeValue = translated;
            } catch (e) {
                console.error("Error traduciendo texto:", e);
            }
        } else if (child.nodeType === Node.ELEMENT_NODE) {
            await translateTextNodes(child, lang); // Recursivamente traduce hijos
        }
    }
}

async function translatePage(lang) {
    const elements = document.querySelectorAll("h1, h2, h3, h4, h5, h6, p, small, button, a, div, strong");

    for (const el of elements) {
        await translateTextNodes(el, lang);
    }
}
