    document.querySelectorAll('.card-image')
    .forEach(img => {

    img.addEventListener('click', function() {

        document.getElementById(
            'imageModal'
        ).style.display = 'block';

        document.getElementById(
            'imageModalContent'
        ).src = this.src;
    });

});


window.closeModal = function() {

    document.getElementById(
        'imageModal'
    ).style.display = 'none';
}

















