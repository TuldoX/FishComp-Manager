const prefix = "http://localhost:8081/api";

class Referee {
    constructor(id, first_name, last_name) {
        this.id = id;
        this.first_name = first_name;
        this.last_name = last_name;
    }
}

class Response {
    constructor(status, message = "", referee = null, token = null) {
        this.status = status;
        this.message = message;
        this.referee = referee;
        this.token = token;
    }
}

const login = async (code, name) => {
    try {
        const response = await fetch(prefix + "/auth/referee", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ name, code })
        });

        const responseData = await response.json();
        const status = response.status;

        if (response.ok && responseData.token && responseData.referee) {
            return new Response(
                status,
                "Login successful.",
                responseData.referee,
                responseData.token
            );
        } else {
            return new Response(
                status,
                responseData.message || "Nesprávne údaje.",
                responseData
            );
        }
    } catch (error) {
        console.error("Login error:", error);
        return new Response(500, "Chyba na serveri.");
    }
};

const submitButton = document.querySelector(".button-element");

document.querySelector(".form").addEventListener("submit", async (event) => {
    event.preventDefault();

    const inputCode = document.getElementById("code").value.trim();
    const inputName = document.getElementById("name").value.trim();
    const message = document.getElementById("message");
    const isValidCode = (code) => /^[a-zA-Z0-9]+$/.test(code);

    if (!inputName || !isValidCode(inputCode) || inputCode.length > 12) {
        message.innerText = "Neplatné údaje.";
        message.classList.replace("message-hidden", "message");
    } else {
        message.classList.replace("message", "message-hidden");
        message.textContent = "";
        submitButton.disabled = true;
        const result = await login(inputCode, inputName);

        if (result.status === 200 && result.referee && result.token) {
            let referee = new Referee(result.referee.id, result.referee.firstName, result.referee.lastName);
            localStorage.setItem("referee", JSON.stringify(referee));
            localStorage.setItem("token", result.token);
            document.cookie = `token=${result.token}; path=/; max-age=3600; SameSite=Lax`;
            window.location.replace("dashboard");
        } else {
            message.textContent = result.message;
            message.classList.replace("message-hidden", "message");
            submitButton.disabled = false;
        }
    }
});

document.querySelector('.back').onclick = () => {
    window.location.replace('/');
};