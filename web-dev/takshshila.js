const express = require('express');
const path = require('path');
const app = express();
const PORT = 5500;

app.use(express.static(path.join(__dirname, 'public')));

// Route for homepage
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'public', 'Home.html'));
});

// Route for test series page
app.get('/test', (req, res) => {
    res.sendFile(path.join(__dirname, 'public', 'test.html'));
});
app.get('/notes', (req, res) => {
    res.sendFile(path.join(__dirname, 'public', 'notes.html'));
});
app.get('/question-bank', (req, res) => {
    res.sendFile(path.join(__dirname, 'public', 'question-bank.html'));
});
app.get('/books', (req, res) => {
    res.sendFile(path.join(__dirname, 'public', 'books.html'));
});

// Start the server
app.listen(PORT, () => {
    console.log(`Server running at http://localhost:${PORT}`);
});
