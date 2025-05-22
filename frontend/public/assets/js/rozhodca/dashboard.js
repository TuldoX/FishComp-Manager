const prefix = "http://localhost:8081/api";

class Competitor {
    constructor(id, first_name, last_name, location, points = 0) {
        this.id = id;
        this.first_name = first_name;
        this.last_name = last_name;
        this.location = location;
        this.points = points;
    }
}

const getCompetitors = async (refereeId) => {
    try {
        const token = localStorage.getItem('token');
        const response = await fetch(prefix + `/referees/${refereeId}/competitors`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error: ${response.status}`);
        }

        return await response.json();
    } catch (err) {
        console.error("Fetch failed:", err);
        throw err;
    }
};


function setCompetitorSession(id, fullName) {
    sessionStorage.setItem("id", JSON.stringify(id));
    sessionStorage.setItem("name", JSON.stringify(fullName));
}

function Render(competitor) {
    const tableBody = document.querySelector('tbody');
    if (!tableBody) return;

    const riadok = document.createElement('tr');

    // Location cell
    const miesto = document.createElement('td');
    miesto.textContent = competitor.location;
    miesto.classList.add('place');
    riadok.appendChild(miesto);

    // Name cell
    const meno = document.createElement('td');
    const a = document.createElement('a');
    a.textContent = `${competitor.first_name[0]}.${competitor.last_name}`;
    a.classList.add('name');
    meno.appendChild(a);
    meno.setAttribute('data-competitor', competitor.id);
    riadok.appendChild(meno);

    // Points cell
    const points = document.createElement('td');
    points.textContent = competitor.points;
    points.classList.add('points');
    riadok.appendChild(points);

    // Button cell
    const button = document.createElement('td');
    const buttonContent = document.createElement('button');
    buttonContent.textContent = '+';
    buttonContent.classList.add('add-catch');
    button.appendChild(buttonContent);
    riadok.appendChild(button);

    tableBody.appendChild(riadok);

    // Name click → view catches
    meno.addEventListener('click', (event) => {
        event.preventDefault();
        const id = meno.dataset.competitor;
        setCompetitorSession(id, `${competitor.first_name} ${competitor.last_name}`);
        window.location.replace("ulovky");
    });

    // Button click → add catch
    buttonContent.addEventListener('click', (event) => {
        event.preventDefault();
        const id = meno.dataset.competitor;
        setCompetitorSession(id, `${competitor.first_name} ${competitor.last_name}`);
        window.location.replace("pridanie_ulovku");
    });
}

// Load referee from localStorage
const referee = JSON.parse(localStorage.getItem("referee"));

if (!referee) {
    window.location.replace("prihlasenie");
}

// Load competitors on page load
window.addEventListener('load', () => {
    const message = sessionStorage.getItem("successMessage");
    if (message) {
        alert(message);
        sessionStorage.clear();
    }

    getCompetitors(referee.id)
        .then(data => {
            data.sort((a, b) => a.location - b.location); // ascending by location number
            data.forEach(element => {
                const competitor = new Competitor(
                    element.id,
                    element.first_name,
                    element.last_name,
                    element.location,
                    element.points || 0 // default to 0 if not present
                );
                Render(competitor);
            });
        })
        .catch(err => {
            console.error(err);
            window.alert('Chyba v požiadavke.');
        });
});

// Logout button
const logoutButton = document.getElementById('koniec');
if (logoutButton) {
    logoutButton.addEventListener('click', () => {
        const confirmed = confirm('Naozaj sa chcete odhlásiť? (Prebehne odoslanie údajov a vy už nebudete môcť nič upraviť.)');
        if (confirmed) {
            localStorage.clear();
            sessionStorage.clear();
            window.location.replace('prihlasenie');
        }
    });
}