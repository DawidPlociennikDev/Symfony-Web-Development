// const { axios } = require("./libraries/axios");

axios.get('http://localhost:8000/api/products').then(function (response) {
    console.log(response);
});