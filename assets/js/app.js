$("message").ready(function () {
    setTimeout(function () {
        $("div.alert").remove();
    }, 10000); // 10 secs
});