var remove_btn = document.getElementById('delete-album');
var remove_path = remove_btn.getAttribute('delete-path');

remove_btn.onclick = (e) => {
    fetch(remove_path, { method: 'DELETE' })
            .then((r) => location.reload()); // once it's deleted, refresh the page
};