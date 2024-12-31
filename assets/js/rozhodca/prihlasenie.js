const getReferee = async () => {
    const response = await fetch('../../data/rozhodca-codes.json');

    if(response.status !== 200){
        throw new Error(`Response status: ${response.status}`);
    }

    const data = await response.json();
    return data;
};

//update - ked sa rozhodca prihlasi zmeni .taken na true aby sa s jeho kodom nedokazal prihlasit nikto iny
const refLoggedIn = async (userId, loggedIn) => {
    const response = await fetch(`https:/fishcomp.sk/dummyUrl`,{ //url bude ine, toto je iba dummy
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ loggedIn }),
    });

    if(response.status !== 200){
        throw new Error(`Response status: ${response.status}`);
    }

    const data = await response.json();
    return data;
}

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

                /*
                refLoggedIn(userid,true)
                    .then( data => {
                        console.log(data);
                    })
                    .catch(err => {
                        message.innerText = 'Chyba v požiadavke';
                        message.classList.replace('message-hidden','message');
                        console.log(err);
                        found = false;
                        localStorage.clear();
                    });
                */

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