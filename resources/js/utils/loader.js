document.addEventListener('DOMContentLoaded', function() {
    const sectionLoaders = document.querySelectorAll('.section-loader');
    sectionLoaders.forEach(loader => {
        setTimeout(() => {
            loader.classList.add('hidden');
        }, 500);
    });
});
