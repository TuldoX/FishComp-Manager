/*
    getCatches() bude post request
    posielat sa bude id pretekara, response od BE budu ulovky s danym id
    nateraz posielam vsetko a js to filtruje
*/

const getCatches = async () => {
    const response = await fetch('../../data/catches.json');

    if(response.status !== 200){
        throw new Error(`Response status: ${response.status}`);
    }

    const data = await response.json();
    return data;
};

/* 
    tento request bude vracat iba jedno meno, opat vsak potrebujem BE
    nateraz vracia vsetky a vyberam
*/

const getCompetitorName = async () => {
    const response = await fetch('../../data/competitors.json');

    if(response.status !== 200){
        throw new Error(`Response status: ${response.status}`);
    }

    const data = await response.json();
    return data;
};

/*
    delete request s id ulovku
*/

window.addEventListener('load',() => {

    const meno = document.querySelector('.name-display');
    const competitorId = sessionStorage.getItem('currentCompetitor');

    getCompetitorName()
    .then(data => {
        const competitor = data.find(el => el.id === competitorId);
        if (competitor) meno.innerText = competitor.name;
    })
    .catch(err => {
        console.log(err);
        meno.innerText = 'Error';
    })

    getCatches()
    .then(data => {

        const table = document.querySelector('tbody');

        data.sort((a,b) => b.points - a.points);

        data.forEach(element => {
            
            if(element.competitorId != competitorId) return;
            
            else {
                const riadok = document.createElement('tr');

                const id = document.createElement('td');
                id.innerText = element.id;
                id.classList.add('fish-id');
                riadok.appendChild(id);

                const species = document.createElement('td');
                species.innerText = element.species;
                species.classList.add('species');
                riadok.appendChild(species);

                const dlzka = document.createElement('td');
                dlzka.innerText = element.points;
                dlzka.classList.add('length');
                riadok.appendChild(dlzka);

                const buttonContainer = document.createElement('td');
                const button = document.createElement('button');
                button.innerText = '-';
                button.classList.add('button-delete');
                buttonContainer.appendChild(button);
                riadok.appendChild(buttonContainer);

                table.appendChild(riadok);
            }
        });

        document.querySelectorAll('.button-delete').forEach( element => {
            element.addEventListener('click', (event) => {
                const confirmed = confirm('Naozaj chcete zmazať úlovok? (Akcia je nenávratná.)');
                if(confirmed){
                    //delete request
                    const riadok = event.target.closest('tr');
                    riadok.remove();
                }
                else{
                    return;
                }
                
            });
        });
    })
    .catch(err => {
        console.log(err);
        window.alert(err);
    });
});

document.querySelector('.back-arrow').addEventListener('click', () => {
    window.location.href = "dashboard.html";
    sessionStorage.clear();
});