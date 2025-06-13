const prefix = "http://localhost:8081/api";

//try catch
const getCatches = async (competitorId) => {
    try {
    const token = localStorage.getItem('token');

    const response = await fetch(prefix + `/competitors/${competitorId}/catches`, {
        headers: {
            'Authorization': `Bearer ${token}`
        }
    });

    if (response.status === 204) {
        return [];
    }

    if (response.status !== 200) {
        throw new Error(`Response status: ${response.status}`);
    }

    return await response.json();
}  catch (err) {
    console.error("Fetch failed:", err);
    throw err;
}};



const deleteCatch = async (catchId) => {
    try {
        const token = localStorage.getItem('token');

        const response = await fetch(prefix + `/catches/${catchId}`, {
            method: "DELETE",
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        if (response.status !== 200) {
            throw new Error(`Response status: ${response.status}`);
        }

        return await response.json();
     }  catch (err) {
    console.error("Fetch failed:", err);
    throw err;
}};


function Render(species, points, id) {
    const row = document.createElement('tr');
    const table = document.querySelector('tbody');

    const speciesCell = document.createElement('td');
    speciesCell.textContent = species;
    speciesCell.classList.add('species');
    row.appendChild(speciesCell);

    const pointsCell = document.createElement('td');
    pointsCell.textContent = points;
    pointsCell.classList.add('length');
    row.appendChild(pointsCell);

    const buttonContainer = document.createElement('td');
    const button = document.createElement('button');
    button.textContent = '-';
    button.classList.add('button-delete');
    button.setAttribute('data-catch',id);

    button.addEventListener("click", async (event) => {
        const confirmed = confirm('Naozaj chcete zmazať úlovok? (Akcia je nenávratná.)');
        if (confirmed) {
            try {
                const catchId = button.dataset.catch;
                await deleteCatch(catchId);
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
    nameDisplay.textContent = JSON.parse(sessionStorage.getItem("name"));

    const competitorId = JSON.parse(sessionStorage.getItem('id'));

    getCatches(competitorId)
        .then(data => {
            if (data.length === 0) {
                return;
            }

            data.sort((a, b) => b.points - a.points);
            data.forEach(element => {
                Render(element.species, element.points, element.id);
            });
        })
        .catch(error => {
            console.error(error);
            window.alert("Nastala chyba.");
        });
});

document.querySelector('.back-arrow').addEventListener('click', () => {
    window.location.replace("dashboard");
    sessionStorage.clear();
});