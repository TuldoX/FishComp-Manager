class Referee {
    constructor(id, first_name, last_name) {
        this.id = id;
        this.first_name = first_name;
        this.last_name = last_name;
    }
}

class Response {
    constructor(status, message = "", data = null) {
        this.status = status;
        this.message = message;
        this.data = data;
    }
}

const login = async (code) => {
    try {
        const response = await fetch("/auth/referee", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ code })
        });

        const responseData = await response.json();
        // console.log("API Response:", responseData); // Debugging

        let responseObject;

        if (response.status >= 200 && response.status < 300) {
            responseObject = new Response(response.status, "OK", responseData.data);
        } else if (response.status === 404) {
            responseObject = new Response(response.status, "Neplatný kód.");
        } else {
            responseObject = new Response(response.status, "Chyba v požiadavke.");
        }

        return responseObject;
    } catch (error) {
        console.error("Login error:", error);
        return new Response(500, "Chyba na serveri.");
    }
};

const submitButton = document.querySelector(".button-element");

document.querySelector(".form").addEventListener("submit", async (event) => {
    event.preventDefault();

    const input = document.getElementById("code").value;
    const message = document.getElementById("message");
    const isValidCode = (code) => /^[a-zA-Z0-9]+$/.test(code);

    if (!isValidCode(input)) {
        message.innerText = "Neplatný kód.";
        message.classList.replace("message-hidden", "message");
    } else {
        submitButton.disabled = true;
        const result = await login(input);

        if (result.status === 200) {
            let referee = new Referee(result.data.id, result.data.first_name,result.data.last_name);
            localStorage.setItem("referee", JSON.stringify(referee));
            // console.log("Referee stored in localStorage:", referee); // Debugging
            window.location.replace("dashboard.html");
        } else {
            message.textContent = result.message;
            message.classList.replace("message-hidden", "message");
            submitButton.disabled = false;
        }
    }
});

document.querySelector('.back').onclick = () => {
    window.location.replace('../../index.html');
};