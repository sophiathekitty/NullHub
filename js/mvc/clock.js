var day_of_week = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

$(document).ready(function(){
    setInterval(MoveTimeBar,1000);
    MoveTimeBar();
});
function MoveTimeBar(){
    var date = new Date(Date.now());
    var secs = date.getSeconds();
    secs += date.getMinutes() * 60;
    secs += date.getHours() * 60 * 60;
    var percent = secs / (60*60*24);
    percent *= 100;
    $(".time_bar").css("left",percent+"%");
    /*
    var hours = date.getHours();
    var am = "am";
    if(hours > 12){
        am = "pm";
        hours -= 12;
    }
    if(hours == 12){
        am = "pm";
    }
    if(hours == 0){
        hours = 12;
    }
    var mins = date.getMinutes();
    if(mins < 10){
        mins = "0"+mins;
    }
    $("#time").html(DateToTimeString(date));
    $("#time").attr("unit",DateToAmString(date));
    $("#time").attr("time",DateToTimeCode(date));
    $("body").attr("time",DateToTimeCode(date));
    $("#date").html(day_of_week[date.getDay()]+", "+months[date.getMonth()]+" "+date.getDate());
    $("#date").attr("date",months[date.getMonth()]+""+date.getDate());
    $("body").attr("date",months[date.getMonth()]+""+date.getDate());
    */
}
function DateToTimeString(date){
    var hours = date.getHours();
    if(hours > 12){
        hours -= 12;
    }
    if(hours == 0){
        hours = 12;
    }
    var mins = date.getMinutes();
    if(mins < 10){
        mins = "0"+mins;
    }
    return hours+":"+mins;
}
function DateToTimeCode(date){
    var hours = date.getHours();
    if(hours > 12){
        hours -= 12;
    }
    if(hours == 0){
        hours = 12;
    }
    var mins = date.getMinutes();
    if(mins < 10){
        mins = "0"+mins;
    }
    return hours+""+mins;
}
function DateToAmString(date){
    var hours = date.getHours();
    var am = "am";
    if(hours >= 12){
        am = "pm";
    }
    return am;
}
function DateToDayPercent(date,offset = 0){
    var hours = date.getHours();
    var min = date.getMinutes()
    return (((((hours+offset)*60) + min) / (24*60))*100)+"%";
}