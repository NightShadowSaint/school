// 1. Import dependencies
const express = require("express");
const app = express();
require("dotenv").config();

// 1.1 Allow parsing on request bodies
app.use(express.json())

// 2. Import routes for api
// To do

// 3. Start server
const port = process.env.PORT || 5000;
app.listen(port, ()=>{
    console.log("Server listening on port ", port);
})