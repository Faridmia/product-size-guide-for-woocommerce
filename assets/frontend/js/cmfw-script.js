const modal = document.getElementById('sizepsgf-sizeguide-modal');
const btn = document.getElementById('cmfw-sizeguidebtn');
const closeBtn = document.querySelector('.sizepsgf-sizechart-close');

if (btn) {
    btn.addEventListener('click', () => {
        if (modal) {
            modal.style.display = 'block';
        }
    });
}

if (closeBtn) {
    closeBtn.addEventListener('click', () => {
        if (modal) {
            modal.style.display = 'none';
        }
    });
}

window.addEventListener('click', (e) => {
    if (modal && e.target === modal) {
        modal.style.display = 'none';
    }
});
