let subMenu = document.getElementById("subMenu");
let subProfile = document.getElementById("subProfile");
let editbox = document.getElementById("editbox");
let subProfile2 = document.getElementById("subProfile2");
let first_name=document.getElementsByClassName("first_name");
let last_name=document.getElementsByClassName("last_name");

/*subProfile.addEventListener('animationend', () => {          //after animation end it will hide the outer profile button
    subProfile.style.display = "none";                        //we can also use onlick event but it is not good for performance and it is not good for user experience
    subProfile2.style.opacity = "1";
});*/

function back() {                                            /* it is use in edit box to get menu box */
    subProfile.classList.remove("open-profile");
    editbox.classList.remove("open-edit");
    console.log("Back button clicked");
}
function edit_box_open() {
    editbox.classList.toggle("open-edit");
    console.log("Hello Sahil");
}
function myprofile() {
    subMenu.classList.toggle("open-menu");
    // Animate profile image into popup
    if (subMenu.classList.contains("open-menu")) {
        subProfile.classList.add("pfp-move");
        document.body.addEventListener("mousedown", closePopupOnOutsideClick);
    } else {
        subProfile.classList.remove("pfp-move");
        document.body.removeEventListener("mousedown", closePopupOnOutsideClick);
    }
    subProfile.classList.toggle("open-profile");
}

function closePopupOnOutsideClick(e) {
    // If click is inside the popup or on the avatar icon, do nothing
    const isInsidePopup = subMenu.contains(e.target);
    const isAvatarIcon = e.target === subProfile || subProfile.contains(e.target);
    if (!isInsidePopup && !isAvatarIcon) {
        subMenu.classList.remove("open-menu");
        subProfile.classList.remove("pfp-move");
        subProfile.classList.remove("open-profile");
        document.body.removeEventListener("mousedown", closePopupOnOutsideClick);
    }
}

function editbtn() {
    // Get the input values using class selectors
    const newFirstName = document.querySelector('.first_name_input').value;
    const newLastName = document.querySelector('.last_name_input').value;
    const newEmail = document.querySelector('.email_input').value;

    // Update the display elements
    document.querySelector('.first_name').textContent = newFirstName;
    document.querySelector('.last_name').textContent = newLastName;
    // Optionally update email if you have a display element for it
    // document.querySelector('.email').textContent = newEmail;

    // Save to localStorage so it persists after reload
    localStorage.setItem('userFirstName', newFirstName);
    localStorage.setItem('userLastName', newLastName);
    localStorage.setItem('userEmail', newEmail);

    // Close the edit box after saving
    editbox.classList.remove('open-edit');

    console.log("Profile updated successfully");
}

// Load profile info from localStorage on page load
window.addEventListener('DOMContentLoaded', function() {
    const savedFirstName = localStorage.getItem('userFirstName');
    const savedLastName = localStorage.getItem('userLastName');
    const savedEmail = localStorage.getItem('userEmail');
    if (savedFirstName) {
        document.querySelector('.first_name').textContent = savedFirstName;
        const input = document.querySelector('.first_name_input');
        if (input) input.value = savedFirstName;
    }
    if (savedLastName) {
        document.querySelector('.last_name').textContent = savedLastName;
        const input = document.querySelector('.last_name_input');
        if (input) input.value = savedLastName;
    }
    if (savedEmail) {
        // If you have a display element for email, update it here
        const input = document.querySelector('.email_input');
        if (input) input.value = savedEmail;
    }
});
function triggerProfileImageInput() {
    document.querySelector('.profile_image_input').click();
  }
  // Store the selected image in localStorage, but do not apply it yet
  function storeSelectedProfileImage(event) {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
      const imageUrl = e.target.result;
      localStorage.setItem('pendingProfileImage', imageUrl);
      // Show preview in the edit image only
      const editImg = document.querySelector('.edit-info .circular-image');
      if (editImg) editImg.src = imageUrl;
    };
    reader.readAsDataURL(file);
  }
  // Apply the stored image everywhere after clicking Edit
  function applyStoredProfileImage() {
    const imageUrl = localStorage.getItem('pendingProfileImage');
    if (imageUrl) {
      document.querySelectorAll('.circular-image').forEach(img => {
        img.src = imageUrl;
      });
      localStorage.setItem('profileImage', imageUrl);
      localStorage.removeItem('pendingProfileImage');
    }
  }
  // Load profile image from localStorage on page load
  window.addEventListener('DOMContentLoaded', function() {
    const savedImage = localStorage.getItem('profileImage');
    if (savedImage) {
      document.querySelectorAll('.circular-image').forEach(img => {
        img.src = savedImage;
      });
    }
  });
  //test tab animation
    let currentProgress = 0; // Global tracking

  // function animateProgressBar(from, to) {
  //   const bar = document.getElementById('progress-bar');
  //   let value = from;
  //   const increment = from < to ? 1 : -1;

  //   // Disable CSS transition temporarily
  //   bar.style.transition = 'none';

  //   const interval = setInterval(() => {
  //     value += increment;
  //     bar.style.width = `${value}%`;
  //     bar.textContent = `${value}% Complete`;

  //     if ((increment > 0 && value >= to) || (increment < 0 && value <= to)) {
  //       clearInterval(interval);
  //       currentProgress = to;

  //       // Re-enable smooth transitions AFTER animation ends
  //       bar.style.transition = 'width 0.7s ease-in-out';
  //     }
  //   }, 15); // speed: adjust if needed
  // }

  function calculateProgress() {
    const testItems = document.querySelectorAll('.test-item');
    let attempted = 0;

    testItems.forEach(item => {
      const marks = parseInt(item.dataset.marks);
      if (!isNaN(marks) && marks > 0) {
        attempted++;
      }
    });

    return Math.round((attempted / testItems.length) * 100);
  }

//   function updateProgressIfNeeded() {
//     const newProgress = calculateProgress();
//     if (newProgress !== currentProgress) {
//       animateProgressBar(currentProgress, newProgress);
//     }
//   }

//   // Initial progress load
//   currentProgress = calculateProgress();
//   animateProgressBar(0, currentProgress);

//   // MutationObserver to detect changes
//   const observer = new MutationObserver(updateProgressIfNeeded);
//   document.querySelectorAll('.test-item').forEach(item => {
//     observer.observe(item, {
//       attributes: true,
//       attributeFilter: ['data-marks']
//     });
//   });

//   // Demo change
//   setTimeout(() => {
//     const test = document.querySelectorAll('.test-item')[1];
//     test.dataset.marks = "30";
//     test.querySelector(".marks").textContent = "30/60";
//   }, 3000);
// //this function is used in notes tab

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
/*Books*/ 
//for the Select the Class,Subject,language and Chapters
        const classSelect = document.getElementById("classSelect");
        const subjectSelect = document.getElementById("subjectSelect");
        const languageSelect = document.getElementById("languageSelect");
        const chapterSelect = document.getElementById("chapterSelect");
        const filterBtn = document.getElementById("filterBtn");
        const results = document.getElementById("results");

        classSelect.addEventListener("change", () => {
            const selectedClass = classSelect.value;
            resetSelect(subjectSelect);
            resetSelect(languageSelect);
            resetSelect(chapterSelect);
            disableSelects([subjectSelect, languageSelect, chapterSelect]);

            if (bookData[selectedClass]) {
                populateOptions(subjectSelect, Object.keys(bookData[selectedClass]));
                subjectSelect.disabled = false;
            }
        });

        subjectSelect.addEventListener("change", () => {
            const selectedClass = classSelect.value;
            const selectedSubject = subjectSelect.value;
            resetSelect(languageSelect);
            resetSelect(chapterSelect);
            disableSelects([languageSelect, chapterSelect]);

            if (bookData[selectedClass]?.[selectedSubject]) {
                populateOptions(languageSelect, Object.keys(bookData[selectedClass][selectedSubject]));
                languageSelect.disabled = false;
            }
        });

        languageSelect.addEventListener("change", () => {
            const selectedClass = classSelect.value;
            const selectedSubject = subjectSelect.value;
            const selectedLanguage = languageSelect.value;
            resetSelect(chapterSelect);
            chapterSelect.disabled = true;

            if (bookData[selectedClass]?.[selectedSubject]?.[selectedLanguage]) {
                populateOptions(chapterSelect, Object.keys(bookData[selectedClass][selectedSubject][selectedLanguage]));
                chapterSelect.disabled = false;
            }
        });

        filterBtn.addEventListener("click", () => {
            const cls = classSelect.value;
            const sub = subjectSelect.value;
            const lang = languageSelect.value;
            const ch = chapterSelect.value;

            results.innerHTML = "<h4 class='books'>--Book Section--</h4>";
            results.style.display = "flex";

            if (!cls) {
                results.innerHTML = "<p>Please select a class.</p>";
                return;
            }
            if (!bookData[cls]) {
    results.innerHTML = "<p>No data available for the selected class.</p>";
    return;
}

            let pdfs = [];
            const subjects = sub ? [sub] : Object.keys(bookData[cls]);

            subjects.forEach(subject => {
                const languages = lang ? [lang] : Object.keys(bookData[cls][subject] || {});
                languages.forEach(language => {
                    const chapters = ch
                        ? [ch]
                        : Object.keys(bookData[cls][subject]?.[language] || {});
                    chapters.forEach(chapter => {
                        const entry = bookData[cls][subject]?.[language]?.[chapter];
                        if (entry) {
                            if (typeof entry === "string") {
                                pdfs.push({ subject, language, chapter, pdf: entry, image: "default.jpg" });
                            } else if (typeof entry === "object") {
                                pdfs.push({ subject, language, chapter, pdf: entry.pdf, image: entry.image });
                            }
                        }
                    });
                });
            });

            if (pdfs.length === 0) {
                results.innerHTML = "<p>No results found.</p>";
                return;
            }

            pdfs.forEach(book => {
                const card = document.createElement("div");
                card.className = "card";
                card.innerHTML = `
      <a class="photo" href="${book.pdf}" target="_blank">
        <img src="${book.image}" alt="${book.chapter}" style="width: 100%; height:auto; margin-bottom:10px" />
      </a>
      <a class="subch" href="${book.pdf}" target="_blank">${book.subject} - ${book.chapter}</a>
      <p class="lang">Language: ${book.language}</p>
    `;
                results.appendChild(card);
            });
        });

        function resetSelect(selectElement) {
            selectElement.innerHTML = '<option value="">Select</option>';
        }

        function populateOptions(selectElement, options) {
            options.forEach(optText => {
                const opt = document.createElement("option");
                opt.value = optText;
                opt.textContent = optText;
                selectElement.appendChild(opt);
            });
        }
        /* blog tab*/ 
        // Add animations when page scrolls
        document.addEventListener('DOMContentLoaded', function() {
            // Animate cards when they come into view
            const cards = document.querySelectorAll('.blog-card');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animation = 'cardEntrance 0.8s ease-out forwards';
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            
            cards.forEach(card => {
                observer.observe(card);
            });
            
            // Add hover effect to cards
            cards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.style.transform = 'translateY(-10px) scale(1.02)';
                    card.style.boxShadow = '0 15px 40px rgba(67, 97, 238, 0.2)';
                });
                
                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'translateY(0) scale(1)';
                    card.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.08)';
                });
            });
            
            // Add newsletter form submission
            const newsletterForm = document.querySelector('.newsletter-form');
            newsletterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const email = this.querySelector('input').value;
                alert(`Thank you for subscribing with ${email}! You'll receive our next newsletter soon.`);
                this.reset();
            });
        });

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