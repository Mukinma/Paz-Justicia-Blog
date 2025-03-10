/* Toggle between adding and removing the "responsive" class to topnav when the user clicks on the icon */
function myFunction() {
        var x = document.getElementById("myTopnav");
        if (x.className === "topnav") {
        x.className += " responsive";
        } else {
        x.className = "topnav";
        }
    }
function scrollMain() {
    var x = document.getElementById("myTopnav");
    x.className = "topnav";
}
function scrollPopular(){
    document.getElementById('scrollButton').addEventListener('click', function() {
        document.getElementById('popular-section').scrollIntoView({ behavior: 'smooth' });
    });
}
document.addEventListener("DOMContentLoaded", function () {
    const track = document.querySelector(".carousel-track");
    const prevBtn = document.querySelector(".prev");
    const nextBtn = document.querySelector(".next");
    let index = 0;

    function updateCarousel() {
        const itemWidth = document.querySelector(".carousel-item").offsetWidth;
        track.style.transform = `translateX(${-index * itemWidth}px)`;
    }

    nextBtn.addEventListener("click", () => {
        if (index < 2) {
            index++;
            updateCarousel();
        }
    });

    prevBtn.addEventListener("click", () => {
        if (index > 0) {
            index--;
            updateCarousel();
        }
    });

    window.addEventListener("resize", updateCarousel);
});