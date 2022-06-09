const time_label = document.querySelector("#time-label");
const time_elem = document.querySelector("#time");
let ctfDate;

fetchTime();

setInterval(fetchTime, 10000);
setInterval(updateTime, 1000);

function updateTime() {
    if (ctfDate) {
        time_elem.innerHTML = formatTime((ctfDate.getTime() - new Date().getTime()) / 1000);
        if (parseInt(ctfDate.getTime() / 1000) == parseInt(new Date().getTime() / 1000)) time_label.innerHTML = "Ended";
    }
}

function fetchTime() {
    fetch(web_server_url + "/api/get-event-info.php")
        .then(response => response.json())
        .then(data => {
            if (data) {
                document.getElementById("countdown-timer").classList.remove("hidden");
                time_label.innerHTML = data.state;
                ctfDate = new Date(data.date);
                updateTime();
            } else {
                document.getElementById("countdown-timer").classList.add("hidden");
            }
            // else {
            //     document.getElementById("countdown-timer").remove()
            // }
        });
}

function formatTime(s) {
    s = Number(s);
    var d = Math.floor(s / (60 * 60 * 24));
    var h = Math.floor(s % (3600 * 24) / 3600);
    var m = Math.floor(s % 3600 / 60);
    var s = Math.floor(s % 60);

    var dDisplay = d > 0 ? String(d).padStart(2, '0') + "<label>DAYS</label> : " : "";
    var hDisplay = h >= 0 ? String(h).padStart(2, '0') + "<label>HOURS</label> : " : "";
    var mDisplay = m >= 0 ? String(m).padStart(2, '0') + "<label>MINUTES</label> : " : "";
    var sDisplay = s >= 0 ? String(s).padStart(2, '0') + "<label>SECONDS</label>" : "";
    return dDisplay + hDisplay + mDisplay + sDisplay;
}