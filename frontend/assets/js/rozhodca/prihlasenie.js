class Referee{
    constructor(id,code){
        this.id = id;
        this.code = code;
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
        const response = await fetch("auth/referee", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ code }),
        });

        const data = await response.json();
        let responseObject;

        if (response.status >= 200 && response.status < 300) {
            responseObject = new Response(response.status, "OK", data);
        } else if (response.status === 404) {
            responseObject = new Response(response.status, "Zadaný kód je neplatný.");
        } else {
            responseObject = new Response(response.status, "Chyba v požiadavke.");
        }

        return responseObject;
    } catch (error) {
        console.error("Login error:", error);
        return new Response(500, "Chyba na serveri.");
    }
};
document.querySelector(".form").addEventListener("submit", async (event) => {
    event.preventDefault();

    const input = document.getElementById("code").value;
    const message = document.getElementById("message");

    const result = await login(input);

    if (result.status === 200) {
        let referee = new Referee(result.data.id, result.data.code);
        localStorage.setItem("referee", JSON.stringify(referee));
        window.location.replace("dashboard.html");
    } else {
        message.innerText = result.message;
        message.classList.replace("message-hidden", "message");
    }
});