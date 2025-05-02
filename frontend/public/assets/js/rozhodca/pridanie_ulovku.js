const prefix = "http://localhost:8081/api";

class Catch{
    constructor(competitor,referee,species, length){
        this.competitor = competitor;
        this.referee = referee;
        this.species = species;
        this.length = length;
    }
}

class Species {
    constructor(id,name,max_length){
        this.id = id;
        this.name = name;
        this.max_length = max_length;
    }
}

const getSpeciesList = async () => {
    try {
        const response = await fetch(prefix + `/species`);
        if (!response.ok) {
            throw new Error(`HTTP error: ${response.status}`);
        }
        return await response.json();
    } catch (err) {
        console.error("Fetch failed:", err);
        throw err;
    }
}

const postCatch = async (ulovok) => {
    const response = await fetch(prefix + '/catches', {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            "competitor": ulovok.competitor,
            "referee": ulovok.referee,
            "species": ulovok.species,
            "length": ulovok.length
        })
    });

    if (response.status === 201) {
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
            return await response.json();
        }
        return null;
    } else {
        let errorMsg = `Chyba ${response.status}`;
        try {
            const errData = await response.json();
            if (errData.message) errorMsg += `: ${errData.message}`;
        } catch (_) {
        }
        throw new Error(errorMsg);
    }
};

function Render(element){
    const select = document.querySelector('.input-select');
    const option = document.createElement('option');
    option.textContent = element.name;
    option.value = element.id;
    option.setAttribute('data-max_length', element.max_length);
    select.appendChild(option);
}

window.addEventListener('load', () => {
    const name = document.querySelector('.name-display');
    name.textContent = JSON.parse(sessionStorage.getItem("name"));

    getSpeciesList()
    .then(data => {
        if(data.length === 0){
            window.alert('Nastala chyba');
            return;
        }
        data.forEach(element => {
            const specie = new Species(element.id,element.name,element.max_length)
            Render(specie);
        });
    })
    .catch(() => {
        window.alert("Nastala chyba.");
    });
});

document.querySelector('.back-arrow').addEventListener('click', () => {
    const druh = document.querySelector('.input-select').value;
    const cm = document.querySelector('.input-number').value;

    if (druh !== "" || cm !== ""){
        const confirmed = confirm('Naozaj chcete opustiť stránku? Zadané údaje sa zahodia.');
        if(confirmed){
            window.location.replace("dashboard");
            sessionStorage.clear();
        }
    }
    else {
        window.location.replace("dashboard");
        sessionStorage.clear();
    }
});

document.querySelector('.button').addEventListener('click', (event) => {
    event.preventDefault();

    const select = document.querySelector('.input-select');
    const selectedOption = select.options[select.selectedIndex];
    const druh = selectedOption.value;
    const cm = Number(document.querySelector('.input-number').value);
    const maxLength = selectedOption.dataset.max_length;

    const button = event.target;
    button.disabled = true;

    if(!druh){
        alert('Vyberte druh!');
        button.disabled = false;
        return;
    }

    if (isNaN(cm) || cm <= 0 || cm > Number(maxLength)) {
        alert('Zadajte platnú dĺžku!');
        button.disabled = false;
        return;
    }

    const competitor = JSON.parse(sessionStorage.getItem('id'));
    const referee = JSON.parse(localStorage.getItem('referee')).id;

    const ulovok = new Catch(competitor,referee,Number(druh),cm);

    postCatch(ulovok)
        .then(() => {
            button.disabled = false;
            sessionStorage.setItem("successMessage", "Úspešne ste pridali úlovok!");
            window.location.replace('ulovky');
        })
        .catch(() => {
            window.alert('Nastala chyba. Skúste pridať úlovok znovu.');
            button.disabled = false;
        });
});