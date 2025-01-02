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

window.addEventListener('load',() => {

    const competitorId = sessionStorage.getItem('currentCompetitor');

    getCatches()
    .then(data => {

        data.forEach(element => {
            if(element.competitorId !== competitorId) return;
            else{
                console.log(element);
            }
        });
    })
    .catch(err => {

    });

});