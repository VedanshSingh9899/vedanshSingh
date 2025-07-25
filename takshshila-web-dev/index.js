const express = require('express');
const path = require('path');
const app = express();
const PORT = 3000;

app.use(express.static(path.join(__dirname, 'src')));

// Route for homepage
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'src','html' ,'Homes.html'));
});
app.get('/login', (req, res) => {
    res.sendFile(path.join(__dirname, 'src','html' ,'Login_page(pending).html'));
});

// Route for test series page
app.get('/test', (req, res) => {
    res.sendFile(path.join(__dirname, 'src', 'html','test_final_ui.html'));
});
app.get('/notes', (req, res) => {
    res.sendFile(path.join(__dirname, 'src', 'html','notes.html'));
});
app.get('/books', (req, res) => {
    res.sendFile(path.join(__dirname, 'src', 'html','books.html'));
});
app.get('/practice-paper', (req, res) => {
    res.sendFile(path.join(__dirname, 'src','html', 'practice_tab.html'));
});
app.get('/test-series', (req, res) => {
    res.sendFile(path.join(__dirname, 'src', 'html','test_series.html'));
});
app.get('/blogs', (req, res) => {
    res.sendFile(path.join(__dirname, 'src', 'html','blog.html'));
});
app.get('/videos', (req, res) => {
    res.sendFile(path.join(__dirname, 'src','html', 'videos.html'));
});
app.get('/contact', (req, res) => {
    res.sendFile(path.join(__dirname, 'src','html', 'contact.html'));
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
