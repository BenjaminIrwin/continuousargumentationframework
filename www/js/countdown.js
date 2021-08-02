function getDebateCountdownTimer(countDownDate) {

    var x = setInterval(function() {

        var state;

        // Get today's date and time
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

        // Find the distance between now and the count down date
        var distance = countDownDate - now;

        // Time calculations for days, hours, minutes and seconds
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Display the result in the element with id="demo"
        document.getElementById("demo").innerHTML = state + ": " + days + "d " + hours + "h "
            + minutes + "m " + seconds + "s ";


    }, 1000);
}