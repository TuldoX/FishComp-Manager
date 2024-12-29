//funkcia na ziskanie udajov o rozhodcoch
const getReferee = async () => {
    const response = await fetch('../../data/rozhodca-codes.json');

    if(response.status !== 200){
        throw new Error(`Response status: ${response.status}`);
    }

    const data = await response.json();
    return data;
};


document.querySelector('form').addEventListener('submit',event =>{
    //aby sa stranka nerefreshovala po odoslani
    event.preventDefault();

    const message = document.getElementById('message');

    getReferee()
        .then(data => {
            const input = document.getElementById('code').value;

            //funkcionalita na skontrolovanie kodu s json suborom
            const found = data.find(element => element.code === input);
            /*
              pôjde asi do fukcie, ktorá vráti true a zmení údaj rozhodcu .taken na true
              a uloží id rozhodcu do localstorage
              const found = findreferee();
            */

            if (found) {
                window.location.href = 'dashboard.html';
            } else {
                message.innerText = 'Zadaný kód je neplatný!';
                message.classList.replace('message-hidden', 'message');
            }
        })
        .catch(err =>{
            //ak je chyba v pripojeni na server
            console.log(err);
            message.innerText = 'Chyba v požiadavke';
            message.classList.replace('message-hidden','message');
        });
});