const getSpecies = async () => {
    const response = await fetch('../../data/species.json');

    if(response.status !== 200){
        throw new Error(`Response status: ${response.status}`);
    }

    const data = await response.json();
    return data;
};

//opat bude posielat competitor ID potom vrati meno, toto je len docasne
const getCompetitorName = async () => {
    const response = await fetch('../../data/competitors.json');

    if(response.status !== 200){
        throw new Error(`Response status: ${response.status}`);
    }

    const data = await response.json();
    return data;
};

window.addEventListener('load', () => {
    const name = document.querySelector('.name-display');
    const competitorId = sessionStorage.getItem('currentCompetitor');

    getCompetitorName()
    .then(data => {
        const competitor = data.find(el => el.id === competitorId);
        if (competitor) name.innerText = competitor.name;
    })
    .catch(err => {
        console.log(err);
        name.innerText = 'Error';
    });

    getSpecies()
    .then(data => {
        const select = document.querySelector('.input-select');
        data.forEach(element => {
            const option = document.createElement('option');
            option.innerText = element.name;
            option.value = element.name;
            select.appendChild(option);
        });
    })
    .catch(err => {
        console.log(err);
    });
});

document.querySelector('.back-arrow').addEventListener('click', () => {
    const Confirm = confirm('Naozaj chcete opustiť stránku? Zadané údaje sa zahodia.');
    if(Confirm){
        window.location.href = "dashboard.html";
        sessionStorage.clear();
    }
    else{
        return;
    }
});

document.querySelector('.button').addEventListener('click',(event) => {
    event.preventDefault();

    const druh = document.querySelector('.input-select').value;
    const cm = document.querySelector('.input-number').value;

    if(cm !== "" && druh !== ""){
        //tu bude put request na update a pridanie úlovku
        window.location.href = 'ulovky.html';
    }
    else{
        alert('Vyberte možnosť a zadajte dĺžku!');
    }
});