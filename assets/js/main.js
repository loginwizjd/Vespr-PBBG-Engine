document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('.slider img');
    let currentIndex = 0;

    function showNextImage() {
        images[currentIndex].style.display = 'none';
        currentIndex = (currentIndex + 1) % images.length;
        images[currentIndex].style.display = 'block';
    }

    images[currentIndex].style.display = 'block';
    setInterval(showNextImage, 3000); // Change image every 3 seconds
});
