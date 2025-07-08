// Call this when user submits login form
function loginUser(username, password) {
  fetch('/login.php', {
    method: 'POST',
    body: new URLSearchParams({ username, password })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      localStorage.setItem('sessionToken', data.token);
      location.reload(); // Or update UI
    } else {
      alert(data.message);
    }
  });
}

// Call this when user clicks logout
function logoutUser() {
  const token = localStorage.getItem('sessionToken');
  if (!token) return;

  fetch('/logout.php', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`
    }
  }).then(() => {
    localStorage.removeItem('sessionToken');
    location.reload(); // Or show login again
  });
}

// Run this on every page load
window.addEventListener('DOMContentLoaded', () => {
  const token = localStorage.getItem('sessionToken');
  if (!token) {
    showLoginUI();
    return;
  }

  fetch('/validate-session.php', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`
    }
  })
  .then(res => res.json())
  .then(data => {
    if (data.valid) {
      showUserProfile(data.user); // Replace with your actual UI function
    } else {
      localStorage.removeItem('sessionToken');
      showLoginUI();
    }
  });
});

// Helpers (replace with actual DOM manipulation)
function showLoginUI() {
  document.getElementById('login-btn').style.display = 'block';
  document.getElementById('user-profile').style.display = 'none';
}

function showUserProfile(user) {
  document.getElementById('login-btn').style.display = 'none';
  const profile = document.getElementById('user-profile');
  profile.innerHTML = `Welcome, ${user.username}`;
  profile.style.display = 'block';
}
