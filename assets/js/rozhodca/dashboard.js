/*
    Takisto ako getCathes() aj getCompetitors bude post request 
    a to akych pretekarov mu zobrazi bude riesit BE
    nateraz je vsetko na FE
*/

const getCompetitors = async () => {
    const response = await fetch('../../data/competitors.json'); 

    if(response.status !== 200){
        throw new Error(`Response status: ${response.status}`);
    }
    const data = await response.json();
    return data;
};

/*
    tu bude http request, ktory pri odhlaseni posle pre istotu update vsetkcyh dat
    konkretneho rozhodcu
    opat na to potrebujem backend , lebo js nedokaze menit json subory
*/

const getCatches = async () => {
    const response = await fetch('../../data/catches.json');

    if(response.status !== 200){
        throw new Error(`Response status: ${response.status}`);
    }

    const data = await response.json();
    return data;
};


window.addEventListener('load', () => {

    document.querySelector('h1').innerText += ' ' + localStorage.getItem('refereeId');

    getCompetitors()
    .then(data => {
        
        const tableBody = document.querySelector('tbody');
        const refereeId = parseInt(localStorage.getItem('refereeId'));

        data.forEach(element => {

            if(element.refId !== refereeId) return;

            else{

                const riadok = document.createElement('tr');

                const id = document.createElement('td');
                id.innerText = element.id;
                id.classList.add('hidden');
                riadok.appendChild(id);

                const miesto = document.createElement('td');
                miesto.innerText = element.place;
                miesto.classList.add('place');
                riadok.appendChild(miesto);

                const meno = document.createElement('td');
                const a = document.createElement('a');
                a.innerText = element.name;
                a.href = '#';
                a.classList.add('name');
                meno.appendChild(a);
                riadok.appendChild(meno);

                const body = document.createElement('td');
                
                getCatches()
                .then(data => {
                    let points = 0;
                    data.forEach(el => {
                        if(element.id !== el.competitorId) return;
                        else points += el.points;
                    });
                    body.innerText = points;
                })
                .catch(err => {
                    console.log(err);
                    body.innerText = 0;
                })

                body.classList.add('points');
                riadok.appendChild(body);

                const button = document.createElement('td');
                const buttonContent = document.createElement('button');
                buttonContent.innerText = '+';
                buttonContent.classList.add('add-catch');
                button.appendChild(buttonContent);
                riadok.appendChild(button);

                tableBody.appendChild(riadok);
            }
        });


        const competitorsHref = document.querySelectorAll('.name');

        competitorsHref.forEach(el => {

            el.addEventListener('click', (event) => {

                const tr = event.target.closest('tr');
                const id = tr.querySelector('.hidden').innerText;
                const name = tr.querySelector('.name').innerText;

                sessionStorage.setItem('currentCompetitor', id);
                sessionStorage.setItem('currentCompetitorName', name);
                
                window.location.href = 'ulovky.html';
                //po odideni z tejto stranky vymazat session storage
            });
        });

        const addCatchButtons = document.querySelectorAll('.add-catch');

        addCatchButtons.forEach(button => {

            button.addEventListener('click', (event) => {

                const tr = event.target.closest('tr');
                const id = tr.querySelector('.hidden').innerText;
                const meno = tr.querySelector('.name').innerText;

                sessionStorage.setItem('currentCompetitor',id);
                sessionStorage.setItem('currentCompetitorName',meno); 
                window.location.href = 'pridanie_ulovku.html';
                //po odideni z tejto stranky vymazat session storage
            });
        });
    })
    .catch(err => {
        window.alert(err);
        console.log(err);
    })
});

document.getElementById('koniec').addEventListener('click', () => {
    
    const confirmed = confirm('Naozaj sa chcete odhlásiť? (Prebehne odoslanie údajov a vy už nebudete môcť nič upraviť.)');

    if(confirmed){
        //tu budem volat posledný update request
        localStorage.clear();
        sessionStorage.clear();
        window.location.replace('prihlasenie.html');
    }
    else{
        return;
    }
});