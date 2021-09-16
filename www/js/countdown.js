/*
    This function was implemented for Arg&Forecast (drawing heavily from: https://www.w3schools.com/howto/howto_js_countdown.asp)
*/
function getDebateCountdownTimer(endDate) {

    var x = setInterval(function() {

        var state;
        var countDownDate = new Date(endDate);
        var now = new Date();

        if(countDownDate < now && countDownDate.setHours(countDownDate.getHours() + 2) > now) {
            state = "VOTING"
            countDownDate = countDownDate.setHours(countDownDate.getHours() + 2);
        } else if (countDownDate < now) {
            document.getElementById("demo").innerHTML = "CLOSED";
            return;
        } else {
            state = "OPEN"
        }

        var distance = countDownDate - now;

        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("clock").innerHTML = state + ": " + days + "d " + hours + "h "
            + minutes + "m " + seconds + "s ";


    }, 1000);
}