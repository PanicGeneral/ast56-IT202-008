function flash(message = "", color = "info") {
    let flash = document.getElementById("flash");
    //create a div (or whatever wrapper we want)
    let outerDiv = document.createElement("div");
    outerDiv.className = "row justify-content-center";
    let innerDiv = document.createElement("div");

    //apply the CSS (these are bootstrap classes which we'll learn later)
    innerDiv.className = `alert alert-${color}`;
    //set the content
    innerDiv.innerText = `(js) ${message}`;

    outerDiv.appendChild(innerDiv);
    //add the element to the DOM (if we don't it merely exists in memory)
    flash.appendChild(outerDiv);
}
function isValidPassword(pass) {
    return pass?.length >= 8;
}
function isValidEmail(email){
    if (!isNotEmpty(email)) return false;
    email = email.trim();
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}
function isValidUsername(username){
    return /^[a-z0-9_-]{3,30}$/.test(username);
}
function isNotEmpty(value){
    return value?.trim().length>0;
}
function isValidConfirm(password, confirm){
    return password === confirm && isNotEmpty(confirm);
}