/**
 * handles displaying the clock data
 */
class ClockView extends View {
    constructor(){
        console.log("ClockView::Constructor");
        super(new ClockModel(),new Template("clock","/templates/stamps/clock.html"));
    }
    /**
     * build the clock widget
     */
    build(){
        console.log("ClockView::Build");
        if(this.template){
            this.template.getData(html=>{
                console.log("ClockView::Build",html);
                $("#clock_stamp").html("");
                $(html).appendTo("#clock_stamp");
                $(html).appendTo(".app main");
                $(".clock").attr("show","sunrise");
                this.display();
                if(this.interval) clearInterval(this.interval);
                this.interval = setInterval(this.refresh.bind(this),1000);
            });
        }
    }
    /**
     * update the time and other datas
     */
    display(){
        //console.log("ClockView::Display");
        if(this.model){
            this.model.getData(json=>{
                //console.log("ClockView::Display",json);
                $(".clock").attr("time_of_day",json.time_of_day);
                $(".clock").attr("day",json.day_of_week);
                $(".clock").attr("month",json.month);
                $(".clock").attr("season",json.season);
                $(".clock").attr("clouds",Math.round(json.clouds/25));
                $(".clock").attr("moon_visible",json.moon_out);
                $(".clock [var=sunrise]").html(this.Time24to12(json.sunrise));
                $(".clock [var=sunrise]").attr("unit",this.Time24toAM(json.sunrise));
                $(".clock [var=sunset]").html(this.Time24to12(json.sunset));
                $(".clock [var=sunset]").attr("unit",this.Time24toAM(json.sunset));
                var phase = "new moon";
                if(json.moon_phase < 1) phase = "waning crescent";
                if(json.moon_phase < 0.75) phase = "waning gibbous";
                if(json.moon_phase < 0.50) phase = "waxing gibbous";
                if(json.moon_phase < 0.25) phase = "waxing crescent";
                if(json.moon_phase == 0.25) phase = "first quarter";
                if(json.moon_phase == 0.50) phase = "full moon";
                if(json.moon_phase == 0.75) phase = "last quarter";
                if(json.moon_phase == 0 || json.moon_phase == 1) phase = "new moon";
                $(".value[var=moon_phase]").html(phase);
                $(".moon").attr("phase",json.moon_phase);
                $(".moon").attr("stage",phase);
                $(".sky").attr("moon_phase",phase);
                $(".moon .disc").css("transform","rotateY("+(360-(360*json.moon_phase))+"deg)");
                $(".sky").attr("afternoon",this.solarNoon(json.sunrise,json.sunset));
                $(".clock [var=day_of_week]").html(json.day_of_week);
                var date = new Date();
                $(".clock [var=date]").html(json.month+" "+date.getDate()+", "+date.getFullYear());
                var h = date.getHours();
                var m = date.getMinutes();
                if(h >= 12) $(".clock [var=time]").attr("unit","pm");
                else $(".clock [var=time]").attr("unit","am");
                if(h > 12) h -= 12;
                if(h == 0) h = 12;
                if(m < 10) m = "0"+m;
                $(".clock [var=time]").html(h+":"+m);
            });
        }
    }
    /**
     * calculates if the current time is past solar noon
     * @param {string} sunrise h:mm (h:mm:ss)
     * @param {string} sunset h:mm (h:mm:ss)
     * @returns {bool} returns true if it's after solar noon
     */
    solarNoon(sunrise,sunset){
        var morning = sunrise.split(":");
        var evening = sunset.split(":");
        var h = Math.round((Number(morning[0]) + Number(evening[0]))/2);
        //console.log("SolarNoon",sunrise,morning,"|",sunset,evening,"||",h);
        var now = new Date();
        return (now.getHours() > h);

    }
    /**
     * acting as an alias for display()
     */
    refresh(){
        //console.log("ClockView::Refresh");
        this.display();
    }
}