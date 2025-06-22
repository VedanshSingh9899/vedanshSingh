const express = require('express');
const path = require('path');
const app = express();
const PORT = 3000;

app.use(express.static(path.join(__dirname, 'src')));

// Route for homepage
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'src', 'Homes.html'));
});

// Route for test series page
app.get('/test', (req, res) => {
    res.sendFile(path.join(__dirname, 'src', 'test.html'));
});
app.get('/notes', (req, res) => {
    res.sendFile(path.join(__dirname, 'src', 'notes.html'));
});
app.get('/question-bank', (req, res) => {
    res.sendFile(path.join(__dirname, 'src', 'question-bank.html'));
});
app.get('/books', (req, res) => {
    res.sendFile(path.join(__dirname, 'src', 'books.html'));
});
app.get('/practice-paper', (req, res) => {
    res.sendFile(path.join(__dirname, 'src', 'practice_tab.html'));
});
app.get('/test-series', (req, res) => {
    res.sendFile(path.join(__dirname, 'src', 'test_series.html'));
});
app.get('/blogs', (req, res) => {
    res.sendFile(path.join(__dirname, 'src', 'blog.html'));
});
app.get('/videos', (req, res) => {
    res.sendFile(path.join(__dirname, 'src', 'videos.html'));
});
app.get('/contact', (req, res) => {
    res.sendFile(path.join(__dirname, 'src', 'contact.html'));
});
app.get('/courses', (req, res) => {
    res.sendFile(path.join(__dirname, 'src', 'courses.html'));
});
app.get('/english.pdf', (req, res) => {
    res.sendFile(path.join(__dirname, 'src', 'temporary', 'english.pdf'));
});
app.get('/hindi.pdf', (req, res) => {
    res.sendFile(path.join(__dirname, 'src', 'temporary', 'hindi.pdf'));
});


// Start the server
app.listen(PORT, () => {
    console.log(`Server running at http://localhost:${PORT}`);
});
