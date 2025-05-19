// Tu clave de API y endpoint
const subscriptionKey = "FhIEJd2BTrDXCjNfnugU5aLIQIZGKZa3OXPMvVwquGJjoH8bfqqSJQQJ99BEACLArgHXJ3w3AAAbACOGrmSy"; // Tu clave de API
const endpoint = "https://api.cognitive.microsofttranslator.com/";
const region = "southcentralus"; // Región de tu servicio

// Función para traducir un texto individual
async function translateText(text, toLang = "en") {
    const url = `${endpoint}/translate?api-version=3.0&to=${toLang}`;
    const headers = {
        "Ocp-Apim-Subscription-Key": subscriptionKey,
        "Ocp-Apim-Subscription-Region": region,
        "Content-Type": "application/json"
    };

    const body = JSON.stringify([{ Text: text }]);

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: headers,
            body: body
        });

        const data = await response.json();

        // Verificar que la API devuelve datos válidos
        if (data && data[0] && data[0].translations[0]) {
            return data[0].translations[0].text;
        } else {
            console.error("Error en la respuesta de la API:", data);
            return text; // Si hay error, devolver el texto original
        }
    } catch (e) {
        console.error("Error al traducir:", e);
        return text; // Si ocurre un error, devolver el texto original
    }
}

// Función para traducir los nodos de texto en el DOM
async function translateTextNodes(node, lang) {
    const childNodes = Array.from(node.childNodes);

    for (const child of childNodes) {
        // Solo traducir los nodos de texto (text nodes)
        if (child.nodeType === Node.TEXT_NODE && child.nodeValue.trim().length > 0) {
            try {
                const translated = await translateText(child.nodeValue, lang);
                child.nodeValue = translated; // Reemplazar el texto original con el traducido
            } catch (e) {
                console.error("Error traduciendo texto:", e);
            }
        } else if (child.nodeType === Node.ELEMENT_NODE) {
            // Recursivamente traducir los nodos de los elementos hijos
            await translateTextNodes(child, lang);
        }
    }
}

// Función principal para traducir la página
async function translatePage(lang) {
    console.log("Traduciendo a:", lang); // Log para verificar que la función se ejecuta

    // Seleccionar los elementos de texto que deseas traducir
    const elements = document.querySelectorAll("h1, h2, h3, h4, h5, h6, p, small, button, a, div, strong");

    // Traducir cada uno de los elementos seleccionados
    for (const el of elements) {
        if (el.textContent.trim().length > 0) {
            await translateTextNodes(el, lang); // Traducir el contenido del elemento
        }
    }
}

// Función para probar la traducción
async function testTranslation() {
    const translated = await translateText("Hola, ¿cómo estás?", "en");
    console.log(translated); // Deberías ver la traducción en inglés en la consola
}

// Llamar a la función de prueba para ver si la API funciona correctamente
testTranslation(); 
