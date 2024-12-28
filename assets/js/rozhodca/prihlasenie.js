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

    getReferee()
        .then(data =>{
            /*tu zavolam funkciu na spracovanie vstupu, ktora
            redirectne na dalsiu stranku alebo ukaze error message
            */ 
        })
        .catch(err =>{
            const message = document.getElementById('message');
            console.log(err);
            message.innerText = 'Chyba v požiadavke';
            message.classList.replace('message-hidden','message');
        });
});