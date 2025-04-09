const express = require('express')
const path=require("path")
const app = express()
const port = 5500
const staticPath=path.join(__dirname,"../public")
app.use(express.static(staticPath))
app.get('/test', (req, res) => {
  res.sendFile('Hello World! Sahil')
})

app.listen(port, () => {
  console.log(`Example app listening on port ${port}`)
})