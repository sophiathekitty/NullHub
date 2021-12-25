/**
 * load in the user and do create user, login user, and logout user functions
 */
class UserModel extends Model {
    constructor(){
        super("user","/api/user","/api/user");
    }
    /**
     * create a new user and update the local 
     * @param {string} username 
     * @param {string} password 
     */
    createUser(username,password){
        console.warn("user signup not implimented");
    }
    /**
     * logs in a user with username and password
     * @param {string} username 
     * @param {string} password 
     */
    login(username,password){
        console.warn("user login not implimented");
    }
    /**
     * pings the logout api to logout the currently logged in user
     */
    logout(callBack){
        console.warn("user logout not implimented");
    }
}