const getCompetitors = async () => {
    // hotove url bude mat query parametre id rozhodcu, napr: https://example.com/data?id=123 - na zaklade toho potom BE posle data
    const response = await fetch('../../data/competitors.json'); 

    if(response.status !== 200){
        throw new Error(`Response status: ${response.status}`);
    }
    const data = await response.json();
    return data;
};

const sendData = async (data) => {
    const response = await fetch('url', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ data }),
    });

    if(response.status !== 200){
        throw new Error(`Response status: ${response.status}`);
    }

    const rep = await response.json();
    return rep;
};

window.addEventListener('load', () => {
    const tableBody = document.querySelector('tbody');

    getCompetitors()
    .then(data => {
        
        data.forEach(element => {
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
            body.innerText = element.points;
            body.classList.add('points');
            riadok.appendChild(body);

            const button = document.createElement('td');
            const buttonContent = document.createElement('button');
            buttonContent.innerText = '+';
            buttonContent.classList.add('add-catch');
            button.appendChild(buttonContent);
            riadok.appendChild(button);

            tableBody.appendChild(riadok);
        });

        const addCatchButtons = document.querySelectorAll('.add-catch');
        addCatchButtons.forEach(button => {
            button.addEventListener('click', (event) => {
                const tr = event.target.closest('tr');
                const id = tr.querySelector('.hidden').innerText;
                sessionStorage.setItem('currentCompetitor',id);
                window.location.href = 'pridanie_ulovku.html';
            });
        });

        const competitorsHref = document.querySelectorAll('.name');
        competitorsHref.forEach(el => {
            el.addEventListener('click', (event) => {
                const tr = event.target.closest('tr');
                const id = tr.querySelector('.hidden').innerText;
                sessionStorage.setItem('currentCompetitor', id);
                window.location.href = 'ulovky.html';
            });
        });
    })
    .catch(err => {
        window.alert(err);
        console.log(err);
    })
});

document.getElementById('koniec').addEventListener('click', () => {
    sendData(data)
    .then(() =>{
        window.location.replace('../uvod.html');
        
    })
    .catch(err => {
        window.alert('Chyba pri odosielaní dát');
        console.log(err);
    });
});