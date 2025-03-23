document.addEventListener('DOMContentLoaded', function() {

    window.showFetchLoader = function () {
        const loader = document.getElementById('fetch-loader');
        if (loader) loader.classList.remove('d-none');
    };

    window.hideFetchLoader = function () {
        const loader = document.getElementById('fetch-loader');
        if (loader) loader.classList.add('d-none');
    };


    const sectionLoaders = document.querySelectorAll('.section-loader');
    sectionLoaders.forEach(loader => {
        setTimeout(() => {
            loader.classList.add('hidden');
        }, 500);
    });
});
