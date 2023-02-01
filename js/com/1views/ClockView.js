/**
 * handles displaying the clock data
 */
class ClockView extends View {
    constructor(debug = false){
        if(debug) console.log("ClockView::Constructor");
        super(new ClockModel(),new Template("clock","/templates/stamps/clock.html",null,60000, debug));
        //super(new ClockModel(),new Template("clock","/widgets/clock.php",null,60000, debug));
        this.pallet = ColorPallet.getPallet("weather");
        this.chart = new HourlyChart("weather_hourly","weather_log","weather_chart","/plugins/NullWeather/api/weather/logs?hourly=1");
        try {
            this.indoor = new TemperaturePixelChart(debug);
            //this.indoor_temp = new IndoorTemperatureView();
            if(debug) console.log("ClockView::Constructor-IndoorTemperatureHourlyChart",this.indoor);
        } catch (error) {
            if(debug) console.warn("ClockView::Constructor-IndoorTemperatureHourlyChart not available",error);
        }
    }
    /**
     * build the clock widget
     * @param {Function} callback callback for when done building
     */
    build(callback){
        if(this.debug) console.log("ClockView::Build");
        if(this.template){
            this.template.getData(html=>{
                if(this.debug) console.log("ClockView::Build",html);
                $("#clock_stamp").html("");
                $(html).appendTo("#clock_stamp");
                $(html).appendTo(".app main");
                $(".clock").attr("show","sunrise");
                //if(this.indoor) this.indoor.buildWeather();
                this.display();
                if(this.interval) clearInterval(this.interval);
                this.interval = setInterval(this.refresh.bind(this),1000);
                if(callback) callback(true);
            });
        }
    }
    /**
     * update the time and other data
     */
    display(){
        if(this.debug) console.log("ClockView::Display");
        if(this.model){
            this.model.getData(json=>{
                if(this.debug) console.log("ClockView::Display",json);
                $(".clock").attr("NullWeather",json.NullWeather);
                $(".clock").attr("NullSensors",json.NullSensors);
                $(".clock").attr("time_of_day",json.time_of_day);
                $(".clock").attr("day",json.day_of_week);
                $(".clock").attr("month",json.month);
                $(".clock").attr("season",json.season);
                $(".clock").attr("clouds",Math.round(json.clouds/25));
                $(".clock").attr("moon_visible",json.moon_out);
                $(".clock").attr("sun_visible",json.sun_out);
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
                /**
                 * 
                 *  Null Weather Stuff
                 * 
                 */
                if(json.NullWeather){
                    $(".clock .outdoors[var=temp]").html(Math.round(json.weather.temp));
                    this.pallet.getColorLerp("temp",json.weather.temp,color=>{
                        $(".clock .outdoors[var=temp]").css("color",color);
                    });
                    $(".clock [var=feels_like]").html(Math.round(json.weather.feels_like));
                    this.pallet.getColorLerp("temp",json.weather.feels_like,color=>{
                        $(".clock [var=feels_like]").css("color",color);
                    });
                    $(".clock [var=humidity]").html(Math.round(json.weather.humidity));
                    this.pallet.getColorLerp("hum",json.weather.feels_like,color=>{
                        $(".clock [var=humidity]").css("color",color);
                    });
                    $(".clock [var=pressure]").html(Math.round(json.weather.pressure));
                    $(".clock [var=wind_speed]").html(Math.round(json.weather.wind_speed));
                    $(".clock [var=wind_speed]").attr("wind_deg",Math.round(json.weather.wind_deg));
                    this.pallet.getColorLerp("wind",json.weather.wind_speed,color=>{
                        $(".clock [var=wind_speed]").css("color",color);
                    });
                    $(".clock [var=description]").html(json.weather.description);
                    $(".clock [var=icon]").attr("icon",json.weather.icon);
                    $(".clock [var=icon]").attr("main",json.weather.main);
                    if(this.chart){
                        // color the weather pixel chart
                        this.chart.getData(json=>{
                            json.weather_log.forEach(hour=>{
                                //console.log(hour);
                                this.pallet.getColorLerp("temp",hour.temp,color=>{
                                    var hours = Number(hour.hour);
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
                                    $(".clock .temp_chart.outdoors [hour="+hour.hour+"]").css("background-color",color);
                                    $(".clock .temp_chart.outdoors [hour="+hour.hour+"]").attr("title","Outdoors -- "+hours+am+"\nTemp: "+Math.round(hour.temp)+"° | "+Math.round(hour.temp_max)+"° / "+Math.round(hour.temp_min)+"°\nHum: "+Math.round(hour.humidity)+"% | "+Math.round(hour.humidity_max)+"% / "+Math.round(hour.humidity_min)+"%");
                                });
                            });
                        });
                    }
                    // rotate the sun and moon
                    this.sunrise_time = json.daytime.sunrise_time;
                    this.sunset_time = json.daytime.sunset_time;
                    
                    if('moonrise_time_yesterday' in json.daytime){
                        var now = Math.floor(Date.now() / 1000);
                        if(now < json.daytime.moonset_time){
                            this.moonrise_time = json.daytime.moonrise_time_yesterday;
                            this.moonset_time = json.daytime.moonset_time;        
                        } else {
                            this.moonrise_time = json.daytime.moonrise_time;
                            this.moonset_time = json.daytime.moonset_time_tomorrow;
                        }
                    } else {
                        this.moonrise_time = json.daytime.moonrise_time;
                        this.moonset_time = json.daytime.moonset_time;    
                    }
                }
                /**
                 * 
                 *  Null Sensors Stuff
                 * 
                 */
                if(json.NullSensors){
                    $(".clock .indoors[var=temp]").html(Math.round(json.indoors.temp));
                    this.pallet.getColorLerp("temp",json.indoors.temp,color=>{
                        $(".clock .indoors[var=temp]").css("color",color);
                    });
                    $(".clock .indoors[var=hum]").html(Math.round(json.indoors.hum));
                    this.pallet.getColorLerp("hum",json.indoors.hum,color=>{
                        $(".clock .indoors[var=hum]").css("color",color);
                    });
                }
            });
        }
        if(this.indoor) this.indoor.displayWeather();
    }
    /**
     * rotate and scale the sun and moon
     */
    displaySunMoon(){
        if('moonset_time' in this){
            var now = Math.floor(Date.now() / 1000);
            var sun_percent = 0;
            var moon_percent = 0;
            
            if(this.sunrise_time < now && now < this.sunset_time){
                sun_percent = (now-this.sunrise_time)/(this.sunset_time-this.sunrise_time);
            }
            if(this.moonrise_time < now && now < this.moonset_time){
                moon_percent = (now-this.moonrise_time)/(this.moonset_time-this.moonrise_time);
            }
            var sun_scale = 4*Math.abs(sun_percent-0.5)+1;
            var moon_scale = 3.5*Math.abs(moon_percent-0.5)+1;
            if(sun_scale < 1) sun_scale = 1;
            if(moon_scale < 1) moon_scale = 1;
            $(".clock #sun_holder").css("transform","rotate("+((130*sun_percent)+35)+"deg)");
            $(".clock #moon_holder").css("transform","rotate("+((260*moon_percent)-20)+"deg)");
            $(".clock #moon_holder .moon").css("transform","rotate("+((-260*moon_percent)+20)+"deg)");
            $(".clock #sun_holder .sun").css("width",sun_scale+"em");
            $(".clock #sun_holder .sun").css("height",sun_scale+"em");
            $(".clock #moon_holder .moon").css("width",moon_scale+"em");
            $(".clock #moon_holder .moon").css("height",moon_scale+"em");
            if(sun_percent <= 0 || sun_percent > 1) $(".clock").attr("sun_visible",0);
            else $(".clock").attr("sun_visible",1);
            if(moon_percent <= 0 || moon_percent > 1) $(".clock").attr("moon_visible",0);
            else $(".clock").attr("moon_visible",1);
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
     * just refreshes the clock
     */
    refresh(){
        //if(this.debug) console.log("ClockView::Refresh");
        //this.display();
        var date = new Date();
        var h = date.getHours();
        var m = date.getMinutes();
        if(h >= 12) $(".clock [var=time]").attr("unit","pm");
        else $(".clock [var=time]").attr("unit","am");
        if(h > 12) h -= 12;
        if(h == 0) h = 12;
        if(m < 10) m = "0"+m;
        $(".clock [var=time]").html(h+":"+m);
        this.displaySunMoon();
    }
}