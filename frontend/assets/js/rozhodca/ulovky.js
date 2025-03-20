class Catch {
    constructor(id, species, points, competitorId) {
        this.id = id;
        this.species = species;
        this.points = points;
        this.competitorId = competitorId;
    }
}

const getCatches = async (competitorId) => {
    const response = await fetch(`/competitors/${competitorId}/catches`);
    if (response.status !== 200) {
        throw new Error(`Response status: ${response.status}`);
    }
    return await response.json();
};

const deleteCatch = async (catchId) => {
    const response = await fetch(`/catches/${catchId}`, { method: "DELETE" });
    if (response.status !== 200) {
        throw new Error(`Response status: ${response.status}`);
    }
    return await response.json();
};

function Render(species, points, id) {
    const row = document.createElement('tr');
    const table = document.querySelector('tbody');

    const speciesCell = document.createElement('td');
    speciesCell.innerText = species;
    speciesCell.classList.add('species');
    row.appendChild(speciesCell);

    const pointsCell = document.createElement('td');
    pointsCell.innerText = points;
    pointsCell.classList.add('length');
    row.appendChild(pointsCell);

    const buttonContainer = document.createElement('td');
    const button = document.createElement('button');
    button.innerText = '-';
    button.classList.add('button-delete');

    button.addEventListener("click", async (event) => {
        const confirmed = confirm('Naozaj chcete zmazať úlovok? (Akcia je nenávratná.)');
        if (confirmed) {
            try {
                await deleteCatch(id);
                const row = event.target.closest('tr');
                row.remove();
            } catch (error) {
                window.alert('Nastala chyba.');
            }
        }
    });

    buttonContainer.appendChild(button);
    row.appendChild(buttonContainer);
    table.appendChild(row);
}

window.addEventListener('load', () => {
    const nameDisplay = document.querySelector('.name-display');
    const competitor = JSON.parse(sessionStorage.getItem("selectedCompetitor"));
    nameDisplay.innerText = competitor.name;

    getCatches(competitor.id)
        .then(data => {
            data.data.sort((a, b) => b.points - a.points);
            const catches = data.data.map(element => new Catch(element.id, element.species, element.points, element.competitorId));
            sessionStorage.setItem("catches", JSON.stringify(catches));

            data.data.forEach(element => {
                Render(element.species, element.points, element.id);
            });
        })
        .catch(error => {
            console.error(error);
            window.alert("Nastala chyba.");
        });
});

document.querySelector('.back-arrow').addEventListener('click', () => {
    window.location.replace("dashboard.html");
    sessionStorage.clear();
});