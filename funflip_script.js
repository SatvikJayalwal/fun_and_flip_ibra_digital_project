document.addEventListener('DOMContentLoaded', () => {
    const COOKIE_NAME = 'uniqueUserID';

    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    function setCookie(name, value, days) {
        const d = new Date();
        d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = "expires=" + d.toUTCString();
        document.cookie = `${name}=${value}; ${expires}; path=/`;
    }

    function generateUniqueUserID() {
        const ipAddress = '192.168.1.54'; // Replace with actual IP retrieval method
        const datetime = new Date().toISOString();
        const salt = 'ibradigitalbrandingservices';
        const rawID = `${ipAddress}_${datetime}_${salt}`;
        return md5(rawID);
    }

    async function fetchMetaInfo(url) {
        const proxyURL = "https://api.allorigins.win/get?url=" + encodeURIComponent(url);

        console.log("Fetching meta info for URL:", url);

        try {
            const response = await fetch(proxyURL);
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            const data = await response.json();

            const parser = new DOMParser();
            const doc = parser.parseFromString(data.contents, "text/html");

            const ogURLMeta = doc.querySelector('meta[property="og:url"]');
            const ogTitleMeta = doc.querySelector('meta[property="og:title"]');

            const websiteURL = ogURLMeta ? ogURLMeta.content.trim() : url;
            const websiteTitle = ogTitleMeta ? decodeURIComponent(ogTitleMeta.content.trim().replace(/<br\s*\/?>/gi, "")) : "No title found";
            const category = websiteURL.split("/")[3] || "No category found";

            console.log("Meta info extracted", { websiteURL, websiteTitle, category });

            localStorage.setItem("fetchedURL", websiteURL);
            localStorage.setItem("fetchedTitle", websiteTitle);
            localStorage.setItem("fetchedCategory", category);

            updateUI(websiteURL, websiteTitle, category);
            const userID = getOrCreateUserID();
            await sendDataToServer({ websiteURL, websiteTitle, category, userID });
        } catch (error) {
            console.error("Error fetching meta info:", error);
        }
    }

    function getOrCreateUserID() {
        let userID = getCookie(COOKIE_NAME);
        if (!userID) {
            userID = generateUniqueUserID();
            setCookie(COOKIE_NAME, userID, 1825); // 5 years
        }
        return userID;
    }

    async function sendDataToServer(data) {
        try {
            const response = await fetch('http://localhost/funflip_main.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }

            const result = await response.json();
            console.log("Data successfully sent to server:", result);
        } catch (error) {
            console.error("Error sending data to PHP server:", error);
            alert("An error occurred while sending data to the server. Please try again later.");
        }
    }

    function updateUI(websiteURL, websiteTitle, category) {
        document.getElementById("website-url").textContent = websiteURL;
        document.getElementById("website-title").textContent = websiteTitle;
        document.getElementById("category").textContent = category;
    }

    const storedURL = localStorage.getItem('fetchedURL');
    const storedTitle = localStorage.getItem('fetchedTitle');
    const storedCategory = localStorage.getItem('fetchedCategory');

    if (storedURL && storedTitle && storedCategory) {
        console.log("Stored data found in localStorage. Displaying fetched meta information.");
        updateUI(storedURL, storedTitle, storedCategory);
        const userID = getOrCreateUserID();
        sendDataToServer({ websiteURL: storedURL, websiteTitle: storedTitle, category: storedCategory, userID});
    } else {
        console.log("No stored data found. Fetching new data.");
        const url = "https://www.funandflip.com/news-detail/my-favorite-apple-tv-shows-including-one-thats-so-good-ive-watched-it-3-times.html";
        fetchMetaInfo(url);
    }
});

// Include an md5 library for generating MD5 hashes
function md5(string) {
    // Add an md5 hash function implementation here or use a library
    // Example: return CryptoJS.MD5(string).toString();
    return CryptoJS.MD5(string).toString();
}
