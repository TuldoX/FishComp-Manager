const getSpecies = async () => {
    const response = await fetch('../../data/species.json');

    if(response.status !== 200){
        throw new Error(`Response status: ${response.status}`);
    }

    const data = await response.json();
    return data;
};

window.addEventListener('load', () => {
    document.querySelector('.name-display').innerText = sessionStorage.getItem('currentCompetitorName');
    getSpecies()
    .then(data => {
        const select = document.querySelector('.input-select');
        data.forEach(element => {
            const option = document.createElement('option');
            option.innerText = element.name;
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