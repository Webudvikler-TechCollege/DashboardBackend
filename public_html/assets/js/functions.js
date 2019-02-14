function getdata() {
    fetch("http://dashboard.local/assets/scripts/getactivities.php")
        .then(response => {
            return response.json();
        })
        .then(data => {
            console.log(data);
        });
    }

setInterval(getdata, 100);