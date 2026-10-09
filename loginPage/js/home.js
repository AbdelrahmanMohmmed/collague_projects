
let users=JSON.parse(localStorage.getItem("user"));

users.forEach(e => {
    if(e.logIn==="true"){
        document.querySelector(".homeContainer").innerHTML=`
        <h1>Welcome ${e.name}</h1>`
    }
});

let logOut=document.getElementById("logOut");

logOut.addEventListener("click",function(e){
    e.preventDefault();
    window.location.href="index.html";
    users.forEach(e => {
    if(e.logIn==="true"){
       e.logIn="";
       localStorage.setItem("user",JSON.stringify(users));
    }
    });
})