const loginTab = document.getElementById('tab-login');
const registerTab = document.getElementById('tab-register');
const loginContent = document.getElementById('pills-login');
const registerContent = document.getElementById('pills-register');

function showTab(tabName) {
  console.log("in")
  if (tabName === 'login') {
    loginTab.classList.add('active');
    registerTab.classList.remove('active');
    loginContent.classList.add('show', 'active');
    registerContent.classList.remove('show', 'active');
  } else if (tabName === 'register') {
    registerTab.classList.add('active');
    loginTab.classList.remove('active');
    registerContent.classList.add('show', 'active');
    loginContent.classList.remove('show', 'active');
  }
}

document.addEventListener('DOMContentLoaded', function () {
  // Get the tabs and content sections
 
  // Add click event listeners to tabs
  loginTab.addEventListener('click', function (e) {
    e.preventDefault();
    showTab('login');
  });

  registerTab.addEventListener('click', function (e) {
    e.preventDefault();
    showTab('register');
  });

  // Function to show the correct tab and content
  
  // Set default tab (optional, in case the markup doesn't preselect one)
  showTab('login'); // Default to login tab
});
