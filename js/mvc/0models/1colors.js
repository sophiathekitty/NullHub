/**
 * ColorPallet
 * Data loading object for color pallet
 * supports simple colors and color arrays for mapping a value to a set of colors
 */
class ColorPallet extends Collection {
    static pallets = [];
    /**
     * gets a color pallet object for the pallet. if it doesn't exit yet it will create the object
     * @param {string} pallet name of the pallet being requested
     * @returns {ColorPallet} returns the requested color pallet object
     */
    static getPallet(pallet){
        //console.log("ColorPallet",ColorPallet.pallets[pallet]);
        if(ColorPallet.pallets[pallet]) return ColorPallet.pallets[pallet];
        return new ColorPallet(pallet);
    }
    /**
     * creates a new color pallet and adds it to the static list of color pallet objects
     * @param {string} pallet the name of the pallet
     */
    constructor(pallet){
        super(pallet,pallet,"/api/colors/pallet/?pallet="+pallet,"/api/colors","id","pallet_");
        this.pull_delay = 100000;
        ColorPallet.pallets[pallet] = this;
        //console.log("ColorPallet::Construct",ColorPallet.pallets[pallet]);
    }
    /**
     * gets a simple color from the pallet
     * @param {String} color the name of the color in the pallet
     * @param {function(string)} callBack the callback function that gets the color string
     * @param {String} alpha the hex alpha values to be added to the end of the color string
     */
    getColor(color,callBack,alpha = ""){
        // get a simple color
        this.getData(data=>{
            if(data) callBack(data[color]+alpha);
        });
    }
    /**
     * get a color from a pallet color that contains an array without blending between colors.
     * @param {String} color the name of the color in the pallet
     * @param {Number} index the value used to pick which color to return. ie: whole number percent (50 is 50%)
     * @param {function(string)} callBack the callback function that gets the color string
     * @param {String} alpha the hex alpha values to be added to the end of the color string
     * @example
     * // returns the color for june
     * pallet.getColorIndex("month",6,color=>{
     *      console.log(color);
     * });
     */
    getColorIndex(color,index,callBack,alpha=""){
        this.getData(data=>{
            if(data){
                callBack(data[color][index]+alpha);
            } 
        });

    }
    /**
     * gets a color from a pallet color that contains an array of colors. blends between the two closest colors
     * @param {String} color the name of the color in the pallet
     * @param {Number} index the value used to pick which color to return. ie: whole number percent (50 is 50%)
     * @param {function(string)} callBack the callback function that gets the color string
     * @param {String} alpha the hex alpha values to be added to the end of the color string
     * @example
     * // solid color
     * pallet.getColorLerp("temp",72,color=>{
     *      console.log(color);
     * });
     * @example
     * // translucent color
     * pallet.getColorLerp("temp",72,color=>{
     *      console.log(color);
     * },"ee");
     */
    getColorLerp(color,index,callBack,alpha = ""){
        // lerp a color
        this.getData(data=>{
            if(data && data.pallet && data.pallet[color]){
                //console.log("color pallet what the fuck is undefined?",color,data);
                if(Object.keys(data.pallet[color]).length == 2){
                    var amount = index / 100;
                    //console.log("color (2) lerp",amount,data.pallet[color],Object.keys(data.pallet[color]).length,data.pallet[color][0],data.pallet[color][1],amount);
                    callBack(this.lerpColor(data.pallet[color][0],data.pallet[color][1],amount)+alpha);
                } else {
                    var amount = (index / 10) - Math.floor(index/10);
                    var i = Math.floor(index / 10);
                    var j = i + 1;
                    if(i < 0) i = 0;
                    if(i > Object.keys(data.pallet[color]).length-1) i = Object.keys(data.pallet[color]).length-1;
                    if(j > Object.keys(data.pallet[color]).length-1) j = Object.keys(data.pallet[color]).length-1;
                    //console.log("color (many) lerp",i,j,amount,data.pallet[color],Object.keys(data.pallet[color]).length,data.pallet[color][i],data.pallet[color][j],amount);
                    callBack(this.lerpColor(data.pallet[color][i],data.pallet[color][j],amount)+alpha);
                }
            } else {
                console.warn("get color pallet lerp data missing? or is color missing?",color,data);
            }
        });
    }
    /**
     * A linear interpolator for hexadecimal colors
     * @param {String} a
     * @param {String} b
     * @param {Number} amount
     * @example
     * // returns #7F7F7F
     * lerpColor('#000000', '#ffffff', 0.5)
     * @returns {String}
     */
    lerpColor(a, b, amount) { 
        if(a == undefined || b == undefined) return "inherit";

        var ah = parseInt(a.replace(/#/g, ''), 16),
            ar = ah >> 16, ag = ah >> 8 & 0xff, ab = ah & 0xff,
            bh = parseInt(b.replace(/#/g, ''), 16),
            br = bh >> 16, bg = bh >> 8 & 0xff, bb = bh & 0xff,
            rr = ar + amount * (br - ar),
            rg = ag + amount * (bg - ag),
            rb = ab + amount * (bb - ab);

        return '#' + ((1 << 24) + (rr << 16) + (rg << 8) + rb | 0).toString(16).slice(1);
    }
}