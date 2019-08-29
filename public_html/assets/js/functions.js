/**
 * Fetcher data til aktiviteter
 */
function getdata() {
    fetch("http://dashboard.local/assets/scripts/getactivities.php")
        .then(response => {
            return response.json();
        })
        .then(data => {
            //Looper data array
            for(var item of data) {
                console.log(item.vcSubject);
            }
        });
    }
setInterval(getdata, 2000);