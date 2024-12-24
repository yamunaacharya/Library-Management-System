function openProfileModal() {
    document.getElementById('profileModal').style.display = 'flex';
}

// to Close Profile Modal
function closeProfileModal() {
    document.getElementById('profileModal').style.display = 'none';
}
function toggleDropdown() {
    const dropdown = document.getElementById('settingsDropdown');
    if (dropdown.style.display === 'block') {
        dropdown.style.display = 'none';
    } else {
        dropdown.style.display = 'block';
    }
}