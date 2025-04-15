class Competitor{
    constructor(id,first_name, last_name,location,points = 0) {
        this.id = id;
        this.first_name = first_name;
        this.last_name = last_name;
        this.location = location;
        this.points = points;
    }
}

const getCompetitors = async (refereeId) => {
    const response = await fetch(`/referees/${refereeId}/competitors`);
    const data = await response.json();

    if(response.status !== 200){
        throw new Error(`Response status: ${response.status}`);
    }
    return data;
};

function Render(competitor) {
    const tableBody = document.querySelector('tbody');
    const riadok = document.createElement('tr');

    const miesto = document.createElement('td');
    miesto.innerText = competitor.location;
    miesto.classList.add('place');
    riadok.appendChild(miesto);

    const meno = document.createElement('td');
    const a = document.createElement('a');
    a.innerText = competitor.first_name[0] + "." + competitor.last_name;
    a.classList.add('name');
    meno.appendChild(a);
    meno.setAttribute('data-competitor',competitor.id);
    riadok.appendChild(meno);

    const points = document.createElement('td');
    points.innerText = competitor.points;
    points.classList.add('points');
    riadok.appendChild(points);

    const button = document.createElement('td');
    const buttonContent = document.createElement('button');
    buttonContent.innerText = '+';
    buttonContent.classList.add('add-catch');
    button.appendChild(buttonContent);
    button.setAttribute('data-competitor',competitor.id);
    riadok.appendChild(button);

    tableBody.appendChild(riadok);

    a.addEventListener('click', (event) => {
        event.preventDefault();
        const id = event.target.closest('[data-competitor]').dataset.competitor;
        sessionStorage.setItem("id", JSON.stringify(id));
        window.location.replace("ulovky.html");
    });

    buttonContent.addEventListener('click', (event) => {
        event.preventDefault();
        const id = event.target.closest('[data-competitor]').dataset.competitor;
        sessionStorage.setItem("id", JSON.stringify(id));
        window.location.replace("pridanie_ulovku.html");
    });
}

const referee = JSON.parse(localStorage.getItem("referee"));

if (!referee) {
    window.location.replace("prihlasenie.html");
}

window.addEventListener('load', () => {
    getCompetitors(referee.id)
        .then(data => {
            const competitors = [];

            data.forEach(element => {
                const competitor = new Competitor(element.id, element.first_name, element.last_name,element.location); //element.points -> zatial 0 - vnorený dotaz
                competitors.unshift(competitor);
                Render(competitor);
            });

            localStorage.setItem("competitors", JSON.stringify(competitors));
        })
        .catch(err => {
            console.log(err);
            window.alert('Chyba v požiadavke.');
        });
});
document.getElementById('koniec').addEventListener('click', () => {
    
    const confirmed = confirm('Naozaj sa chcete odhlásiť? (Prebehne odoslanie údajov a vy už nebudete môcť nič upraviť.)');

    if(confirmed){
        localStorage.clear();
        sessionStorage.clear();
        window.location.replace('prihlasenie.html');
    }
})