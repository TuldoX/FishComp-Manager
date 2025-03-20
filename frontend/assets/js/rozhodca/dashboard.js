class Competitor{
    constructor(id,name,place,points) {
        this.id = id;
        this.name = name;
        this.place = place;
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

function FormatName(fullName) {
    let nameParts = fullName.trim().split(" ");
    if (nameParts.length < 2) return fullName;

    let firstInitial = nameParts[0][0].toUpperCase();
    let lastName = nameParts[nameParts.length - 1];

    return `${firstInitial}.${lastName}`;
}

function Render(competitor) {
    const tableBody = document.querySelector('tbody');
    const riadok = document.createElement('tr');

    const miesto = document.createElement('td');
    miesto.innerText = competitor.place;
    miesto.classList.add('place');
    riadok.appendChild(miesto);

    const meno = document.createElement('td');
    const a = document.createElement('a');
    a.innerText = FormatName(competitor.name);
    a.classList.add('name');
    meno.appendChild(a);
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
    riadok.appendChild(button);

    tableBody.appendChild(riadok);

    a.addEventListener('click', (event) => {
        event.preventDefault(); // Prevent default link behavior
        const formattedName = event.target.innerText;
        const competitors = JSON.parse(localStorage.getItem("competitors")) || [];
        const selectedCompetitor = competitors.find(comp => FormatName(comp.name) === formattedName);
        sessionStorage.setItem("selectedCompetitor", JSON.stringify(selectedCompetitor));
        window.location.replace("ulovky.html");
    });

    buttonContent.addEventListener('click', (event) => {
        event.preventDefault();
        const formattedName = FormatName(competitor.name); // Use the competitor's name directly
        const competitors = JSON.parse(localStorage.getItem("competitors")) || [];
        const selectedCompetitor = competitors.find(comp => FormatName(comp.name) === formattedName);
        sessionStorage.setItem("selectedCompetitor", JSON.stringify(selectedCompetitor));
        window.location.replace("pridanie_ulovku.html");
    });
}

const referee = JSON.parse(localStorage.getItem("referee"));

if (!referee) {
    window.location.replace("prihlasenie.html");
}

console.log("Referee retrieved from localStorage:", referee); // Debugging
window.addEventListener('load', () => {
    getCompetitors(referee.id)
        .then(data => {
            const competitors = [];

            data.data.forEach(element => {
                const competitor = new Competitor(element.id, element.name, element.place, element.points);
                competitors.unshift(competitor);
                Render(competitor);
            });

            localStorage.setItem("competitors", JSON.stringify(competitors));
            console.log("Competitors stored in localStorage:", competitors); // Debugging
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