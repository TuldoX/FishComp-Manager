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
    let userid = null;
    const message = document.getElementById('message');

    getReferee()
        .then(data => {
            const input = document.getElementById('code').value;
            let found = false;
            let sprava = null;

            //logika na prihlasenie rozhodcu
            for (let i = 0; i < data.length; i++) {
                if (data[i].code === input) {
                    if (data[i].taken) {
                        sprava = 'Zadaný rozhodca už je prihlásený!';
                        found = true;
                    } else {
                        userid = data[i].id;
                        found = true;
                    }
                    break;
                }
            }

            if(found && sprava === null){
                //tu by mala byt funkcia na update json suboru ale na to potrebujem backend - zmena .taken na true
                //ulozenie id do sessionstorage aby som mohol retrievnut data na dashboard
                sessionStorage.setItem('userId',userid);
                window.location.href = 'dashboard.html';
            }
            else if(found && sprava !== null){
                message.innerText = sprava;
                message.classList.replace('message-hidden','message');
                sprava = null;
                found = false;
            }
            else{
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