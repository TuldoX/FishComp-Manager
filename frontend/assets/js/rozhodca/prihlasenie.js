const getReferee = async () => {
    const response = await fetch('../../data/referees.json');

    if(response.status !== 200){
        throw new Error(`Response status: ${response.status}`);
    }
    let data = await response.json();
    return data;
};

/*
    treba este dalsi http request na zmenu .taken na true
    k tomu vsak treba backend (samotne js nedokaze upravovat subory)
*/

document.querySelector('form').addEventListener('submit',event =>{
    event.preventDefault();
    let userid = null;
    const message = document.getElementById('message');

    getReferee()
        .then(data => {
            let found = false;
            let sprava = null;
            const input = document.getElementById('code').value;
        
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
                localStorage.setItem('refereeId',userid);
                window.history.replaceState(null, null, 'dashboard.html');
                window.location.replace('dashboard.html');
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
            console.log(err);
            message.innerText = 'Chyba v požiadavke';
            message.classList.replace('message-hidden','message');
        });
});