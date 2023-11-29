<<<<<<< HEAD:public/build/assets/categories-104f93b8.js
import{ai as a,D as s}from"./app-727d01fd.js";const r=a("categories",{state:()=>({categories:[]}),getters:{list:e=>e.categories},actions:{async getCategories(e){await s(e).get("/api/category/state/category-list").then(t=>{this.categories=Object.assign([],t.data)}).catch(t=>{console.log("getCategories error: ",t)})}}});export{r as u};
=======
import{al as a,D as s}from"./app-d45ea034.js";const r=a("categories",{state:()=>({categories:[]}),getters:{list:e=>e.categories},actions:{async getCategories(e){await s(e).get("/api/category/state/category-list").then(t=>{this.categories=Object.assign([],t.data)}).catch(t=>{console.log("getCategories error: ",t)})}}});export{r as u};
>>>>>>> 463d1ee4749f7103e261ead6f4fb8f39092fbc01:public/build/assets/categories-96fab99a.js
