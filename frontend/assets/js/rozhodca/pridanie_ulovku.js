class Catch{
    constructor(name,points,competitorId){
        this.species = name;
        this.points = points;
        this.competitorId = competitorId;
    }
}

const getSpeciesList = async () => {
    const response = await fetch('/species');
    const data = await response.json();

    if(response.status !== 200){
        throw new Error(`Response status: ${response.status}`);
    }

    return data;
}

const postCatch = async (ulovok) => {
    const response = await fetch('/catches',{
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            "competitorId": ulovok.competitorId,
            "druh": ulovok.species,
            "points": ulovok.points
        })
    });

    const odpoved = await response.json();
    if(response.status !== 200){
        throw new Error(`Response status: ${response.status}`);
    }

    return odpoved;
}

const competitor = JSON.parse(sessionStorage.getItem('selectedCompetitor'));

window.addEventListener('load', () => {
    const name = document.querySelector('.name-display');
    name.innerText = competitor.name;

    getSpeciesList()
    .then(data => {
        const select = document.querySelector('.input-select');
        data.data.forEach(element => {
            const option = document.createElement('option');
            option.innerText = element.name;
            option.value = element.name;
            select.appendChild(option);
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
        const Confirm = confirm('Naozaj chcete opustiť stránku? Zadané údaje sa zahodia.');
        if(Confirm){
            window.location.replace("dashboard.html");
            sessionStorage.clear();
        }
    }
    else {
        window.location.replace("dashboard.html");
        sessionStorage.clear();
    }
});

document.querySelector('.button').addEventListener('click', (event) => {
    event.preventDefault();

    const druh = document.querySelector('.input-select').value;
    const cm = Number(document.querySelector('.input-number').value);

    if (isNaN(cm) || cm <= 0) {
        alert('Zadajte platnú dĺžku!');
        return;
    }

    const ulovok = new Catch(druh, cm, competitor.id);
    console.log(ulovok);

    if (druh !== "") {
        postCatch(ulovok)
            .then(response => {
                console.log(response);
                window.location.replace('ulovky.html');
            })
            .catch(() => {
                window.alert('Nastala chyba. Skúste pridať úlovok znovu.');
            });
    } else {
        alert('Vyberte možnosť!');
    }
});