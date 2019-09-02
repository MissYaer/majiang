

function sendMsg(data) {
    data.random_cookie = random_cookie;
    data = JSON.stringify( data );
    console.log(data);
    ws.send(data)
}

