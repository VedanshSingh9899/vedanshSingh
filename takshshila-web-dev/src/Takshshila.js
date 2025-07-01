let subMenu = document.getElementById("subMenu");
let subProfile = document.getElementById("subProfile");
let editbox = document.getElementById("editbox");
let subProfile2 = document.getElementById("subProfile2");
/*subProfile.addEventListener('animationend', () => {          //after animation end it will hide the outer profile button
    subProfile.style.display = "none";                        //we can also use onlick event but it is not good for performance and it is not good for user experience
    subProfile2.style.opacity = "1";
});*/

function back() {                                            /* it is use in edit box to get menu box */
    subProfile.classList.remove("open-profile");
    editbox.classList.remove("open-edit");
    console.log("Back button clicked");
}
/*function back2() {   */                                         /* it is use to close menu box */
/*subProfile.classList.remove("open-menu");
console.log("Back 2 button clicked");
}*/
function editbtn() {
    editbox.classList.toggle("open-edit");
    console.log("Hello Sahil");
}
function myprofile() {
    subMenu.classList.toggle("open-menu");
    subProfile.classList.toggle("open-profile");
}
//this function is used in notes tab

function matchAndLoadPDF() {
            const subject = document.getElementById('subject').value;
            const language = document.getElementById('language').value;
            const chapter = document.getElementById('chapter').value;

            // Hide all initially
            document.getElementById("english").style.display = 'none';
            document.getElementById("hindi").style.display = 'none';
            document.getElementById("filterResults").style.display = 'none';

            if (subject === 'english' && language === 'english' && chapter === 'chapter-1')
            {
                document.getElementById("filterResults").style.display = 'block';
                document.getElementById("english").style.display = 'block';
            } else if (subject === 'hindi' && language === 'hindi' && chapter === 'chapter-1') {
                document.getElementById("filterResults").style.display = 'block';
                document.getElementById("hindi").style.display = 'block';
            } else {
                console.log("No matching result.");
            }
        }
function applyTheme() {
    const theme = localStorage.getItem('theme');
    if (theme === 'dark') {
        document.body.classList.add('dark-mode');
        document.querySelector('.toggle-btn').textContent = '🌙 Dark Mode';
    } else {
        document.body.classList.remove('dark-mode');
        document.querySelector('.toggle-btn').textContent = '☀️ Light Mode';
    }
}

function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    const isDark = document.body.classList.contains('dark-mode');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    document.querySelector('.toggle-btn').textContent = isDark ? '🌙 Dark Mode' : '☀️ Light Mode';
}

// Run this when the page loads
window.onload = applyTheme;